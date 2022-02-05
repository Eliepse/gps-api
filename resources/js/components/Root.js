import { Route, Routes } from "react-router-dom";
import { IndexPage } from "pages";
import { LivePage } from "pages/live";
import { useEffect } from "react";
import { useDispatch } from "react-redux";
import { hydrateUser } from "store/slices/userSlice";

export const Root = () => {
	const dispatch = useDispatch();

	useEffect(() => {
		window.axios
			.get("/api/me")
			.then((res) => dispatch(hydrateUser(res.data)))
			.catch(console.error);
	}, [dispatch]);

	return (
		<Routes>
			<Route path="/" element={<IndexPage />} />
			<Route path="/live" element={<LivePage />} />
		</Routes>
	);
};
