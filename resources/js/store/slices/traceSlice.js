import { createSlice } from "@reduxjs/toolkit";
import { calcSpeed, msToKmh } from "lib/supports/number";
import dayjs from "dayjs";

const initialState = null;

export const slice = createSlice({
	name: "trace",
	initialState,
	reducers: {
		hydrate: (state, action) => {
			if (!action.payload) {
				return;
			}

			return {
				...action.payload,
				coordinates: action.payload.coordinates || state.coordinates || []
			};
		},
		start: (state, action) => {
			if (!action.payload) {
				return;
			}

			const startedAt = dayjs(action.payload.started_at);
			const timeDelta = dayjs().diff(startedAt);

			return { ...action.payload, coordinates: [], started_at: startedAt.add(timeDelta) };
		},
		stop: (state, action) => {
			if (!action.payload) {
				return;
			}

			const finishedAt = dayjs(action.payload.finished_at);
			const timeDelta = dayjs().diff(finishedAt);
			const correctedFinishedAt = finishedAt.add(timeDelta);

			// Update average speed
			if (state.started_at && state.length > 0) {
				const duration = dayjs.duration(correctedFinishedAt.diff(state.started_at)).asSeconds();
				state.averageSpeed = msToKmh(calcSpeed(state.length, duration));
			}

			return { ...state, ...action.payload, finished_at: correctedFinishedAt };
		},
		addCoordinates: (state, action) => {
			state.coordinates = [...state.coordinates, ...action.payload];
		},
		updateLength: (state, action) => {
			state.length = action.payload;

			// Update average speed
			if (state.started_at && action.payload > 0) {
				const duration = dayjs.duration(dayjs(state.finished_at).diff(state.started_at)).asSeconds();
				state.averageSpeed = msToKmh(calcSpeed(action.payload, duration));
			}
		}
	}
});

// Action creators are generated for each case reducer function
export const {
	hydrate: hydrateTrace,
	start: startTrace,
	stop: stopTrace,
	addCoordinates,
	updateLength
} = slice.actions;

export default slice.reducer;
