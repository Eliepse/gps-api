import { createSlice } from "@reduxjs/toolkit";

const initialState = {};

export const slice = createSlice({
	name: "trackers",
	initialState,
	reducers: {
		hydrate: (state, action) => action.payload,
		add: (state, action) => {
			return [...state, action.payload];
		},
		remove: (state, action) => {
			return state.filter((tracker) => tracker.payload.id !== action.payload.id);
		},
	},
});

// Action creators are generated for each case reducer function
export const { hydrate: hydrateTrackers, add: addTracker, remove: removeTracker } = slice.actions;

export default slice.reducer;
