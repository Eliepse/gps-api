import { createMachine, guard, invoke, reduce, state, transition } from "robot3";
import { api } from "lib/api/axios";

/*
 |------------------
 | Guards
 |------------------
 */

function trackerAvailable(state) {
	return Boolean(state.tracker);
}

function traceIsNotFinished(state) {
	return state.trace && !state.trace.finished_at;
}

function traceIsFinished(state) {
	return !state.trace || state.trace?.finished_at;
}

/*
 |------------------
 | Reducers
 |------------------
 */

function updateTracker(ctx, { type, data }) {
	if (type === "trackerOffline") {
		return { ...ctx, tracker: undefined };
	}

	return { ...ctx, tracker: data.tracker };
}

function updateTrace(ctx, { data }) {
	return { ...ctx, trace: data };
}

function updateCoordinates(ctx, { data }) {
	return { ...ctx, path: [...ctx.path, ...data.coordinates] };
}

function reduceInitData(ctx, { data }) {
	return { ...ctx, trace: data.trace, path: data.path };
}

/*
 |------------------
 | Invokes
 |------------------
 */

function createTrace(ctx) {
	return new Promise((resolve, reject) => {
		api
			.post("/trace", { tracker_uid: ctx.tracker.uid })
			.then(({ data }) => resolve(data))
			.catch(reject);
	});
}

function stopTrace(ctx) {
	return new Promise((resolve, reject) => {
		api
			.post(`/trace/${ctx.trace.uid}/stop`)
			.then(({ data }) => resolve(data))
			.catch(reject);
	});
}

/*
 |------------------
 | Machine
 |------------------
 */

export const liveMachine = createMachine(
	"init",
	{
		init: state(transition("freshStart", "offline"), transition("alreadyRecording", "offline", reduce(reduceInitData))),
		offline: state(
			transition("trackerOnline", "waitLocation", reduce(updateTracker), guard(traceIsFinished)),
			transition("trackerOnline", "recording", reduce(updateTracker), guard(traceIsNotFinished)),
		),
		waitLocation: state(
			transition("locationUpdated", "ready"),
			transition("trackerOffline", "offline", reduce(updateTracker)),
		),
		ready: state(
			transition("startTracking", "preRecording"),
			transition("trackerOffline", "offline", reduce(updateTracker)),
		),
		preRecording: invoke(
			createTrace,
			transition(
				"done",
				"recording",
				reduce((ctx) => ({ ...ctx, path: [] })),
				reduce(updateTrace),
			),
			transition("error", "ready"),
		),
		recording: state(
			transition("stopTracking", "postRecording"),
			transition("updateTrace", "recording", reduce(updateCoordinates)),
			transition("trackerOffline", "offline", reduce(updateTracker)),
		),
		postRecording: invoke(
			stopTrace,
			transition("done", "ready", reduce(updateTrace)),
			transition("error", "recording"),
		),
	},
	() => ({
		path: [],
		trace: undefined,
		tracker: undefined,
	}),
);
