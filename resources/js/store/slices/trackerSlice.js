import { createSlice } from "@reduxjs/toolkit";
import { emptyObj } from "lib/supports/object";

const initialState = {
	tracker: { active: false },
	metadata: {}
};

export const slice = createSlice({
	name: "tracker",
	initialState,
	reducers: {
		hydrate: (state, action) => {
			state.tracker = action.payload;
		},
		connect: (state) => {
			state.tracker.active = true;
		},
		disconnect: (state) => {
			state.metadata = {};
			state.tracker.active = false;
		},
		updateMetadata: (state, action) => {
			const { activeSatellitesCount, visibleSatellitesCount } = action.payload;
			state.tracker.active = true;
			state.metadata = {
				coordinate: action.payload.coordinate?.length === 2 ? action.payload.coordinate : [],
				satellites: { active: activeSatellitesCount, visible: visibleSatellitesCount },
				precision: action.payload.precision || 25
			};
		}
	}
});

export const getTracker = (state) => state.tracker.tracker;
export const getTrackerMetadata = (state) => state.tracker.metadata || emptyObj;
export const isTrackerOnline = (state) => state.tracker.tracker.active === true;

// Action creators are generated for each case reducer function
export const {
	hydrate: hydrateTrackers,
	connect: connectTracker,
	disconnect: disconnectTracker,
	updateMetadata: updateTrackerMetadata
} = slice.actions;

export default slice.reducer;
