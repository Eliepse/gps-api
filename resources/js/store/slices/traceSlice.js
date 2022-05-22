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

			return { ...action.payload, coordinates: [] };
		},
		stop: (state, action) => {
			if (!action.payload) {
				return;
			}

			// Update average speed
			if (state.started_at && state.length > 0) {
				const duration = dayjs.duration(dayjs(state.finished_at).diff(state.started_at)).asSeconds();
				state.averageSpeed = msToKmh(calcSpeed(state.length, duration));
			}

			return { ...state, ...action.payload };
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
