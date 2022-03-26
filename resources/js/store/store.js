import { configureStore } from "@reduxjs/toolkit";
import userReducer from "store/slices/userSlice";
import trackersReducer from "store/slices/trackersSlice";
import traceReducer from "store/slices/traceSlice";

export const store = configureStore({
	reducer: {
		user: userReducer,
		trackers: trackersReducer,
		trace: traceReducer,
	},
});
