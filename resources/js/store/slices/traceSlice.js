import { createSlice } from "@reduxjs/toolkit";

const initialState = {};

export const slice = createSlice({
	name: "trace",
	initialState,
	reducers: {
		hydrate: (state, action) => action.payload,
	},
});

// Action creators are generated for each case reducer function
export const { hydrate: hydrateTrace } = slice.actions;

export default slice.reducer;
