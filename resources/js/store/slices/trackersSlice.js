import { createSlice } from "@reduxjs/toolkit";
import { emptyObj } from "lib/supports/object";

const initialState = {
	trackers: [],
	metadata: {},
};

export const slice = createSlice({
	name: "trackers",
	initialState,
	reducers: {
		hydrate: (state, action) => {
			state.trackers = action.payload;
		},
		add: (state, action) => {
			state.trackers = [...state.trackers, action.payload];
		},
		remove: (state, action) => {
			delete state.metadata[action.payload.id];
			state.trackers = state.trackers.filter((tracker) => tracker.payload.id !== action.payload.id);
		},
		updateMetadata: (state, action) => {
			if (!action.payload.tracker.uid || !action.payload.metadata) {
				return;
			}

			state.metadata[action.payload.tracker.uid] = action.payload.metadata;
		},
	},
});

export const getTrackers = (state) => state.trackers.trackers;
export const getTrackerMetadata = (uid) => (state) => state.trackers.metadata[uid] || emptyObj;

// Action creators are generated for each case reducer function
export const {
	hydrate: hydrateTrackers,
	add: addTracker,
	remove: removeTracker,
	updateMetadata: updateTrackerMetadata,
} = slice.actions;

export default slice.reducer;
