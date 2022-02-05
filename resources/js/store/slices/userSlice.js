import { createSlice } from "@reduxjs/toolkit";

const initialState = {};

export const slice = createSlice({
	name: "user",
	initialState,
	reducers: {
		hydrate: (state, action) => action.payload,
	},
});

// Action creators are generated for each case reducer function
export const { hydrate: hydrateUser } = slice.actions;

export function getUserId(state) {
	return state.user.id;
}

export default slice.reducer;
