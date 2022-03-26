import { configureStore } from "@reduxjs/toolkit";
import userReducer from "store/slices/userSlice";
import trackersReducer from "store/slices/trackersSlice";

export const store = configureStore({
	reducer: {
		user: userReducer,
		trackers: trackersReducer
	}
});
