import { createSlice } from "@reduxjs/toolkit";

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
				coordinates: action.payload.coordinates || state.coordinates || [],
			};
		},
		start: (state, action) => {
			if (!action.payload) {
				return;
			}

			return { ...action.payload, coordinates: [] };
		},
		stop: (state, action) => {
			if (!action.payload) {
				return;
			}

			return { ...state, ...action.payload };
		},
		addCoordinates: (state, action) => {
			state.coordinates = [...state.coordinates, ...action.payload];
		},
		updateLength: (state, action) => {
			state.length = action.payload;
		},
	},
});

// Action creators are generated for each case reducer function
export const { hydrate: hydrateTrace, start: startTrace, stop: stopTrace, addCoordinates, updateLength } = slice.actions;

export default slice.reducer;
