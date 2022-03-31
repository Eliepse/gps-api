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
				coordinates: action.payload.coordinates || [],
			};
		},
		addCoordinates: (state, action) => {
			state.coordinates = [...state.coordinates, ...action.payload];
		},
	},
});

// Action creators are generated for each case reducer function
export const { hydrate: hydrateTrace, addCoordinates } = slice.actions;

export default slice.reducer;
