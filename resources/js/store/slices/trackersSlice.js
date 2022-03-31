import { createSlice } from "@reduxjs/toolkit";
import { emptyObj } from "lib/supports/object";

const initialState = {
	trackers: {},
	metadata: {},
};

export const slice = createSlice({
	name: "trackers",
	initialState,
	reducers: {
		hydrate: (state, action) => {
			state.trackers = Object.fromEntries(action.payload.map((tracker) => [tracker.uid, tracker]));
		},
		connect: (state, action) => {
			state.trackers[action.payload.id].active = true;
		},
		disconnect: (state, action) => {
			delete state.metadata[action.payload.id];
			state.trackers[action.payload.id].active = false;
			//state.trackers = state.trackers.filter((tracker) => tracker.payload.id !== action.payload.id);
		},
		updateMetadata: (state, action) => {
			const { tracker, coordinate, activeSatellitesCount, visibleSatellitesCount } = action.payload;

			if (!tracker?.uid) {
				return;
			}

			state.metadata[action.payload.tracker.uid] = {
				coordinate,
				satellites: { active: activeSatellitesCount, visible: visibleSatellitesCount },
			};
		},
	},
});

export const getTrackers = (state) => state.trackers.trackers;
export const getTrackerMetadata = (uid) => (state) => state.trackers.metadata[uid] || emptyObj;
export const hasOnlineTracker = (state) => {
	return Object.values(state.trackers.trackers).some((tracker) => tracker.active === true);
};

// Action creators are generated for each case reducer function
export const {
	hydrate: hydrateTrackers,
	connect: connectTracker,
	disconnect: disconnectTracker,
	updateMetadata: updateTrackerMetadata,
} = slice.actions;

export default slice.reducer;
