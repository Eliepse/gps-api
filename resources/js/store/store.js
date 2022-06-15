import { configureStore } from "@reduxjs/toolkit";
import userReducer from "store/slices/userSlice";
import trackerReducer from "store/slices/trackerSlice";
import traceReducer from "store/slices/traceSlice";

export const store = configureStore({
	reducer: {
		user: userReducer,
		tracker: trackerReducer,
		trace: traceReducer
	}
});
