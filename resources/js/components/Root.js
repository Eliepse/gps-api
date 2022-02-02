import { Route, Routes } from "react-router-dom";
import { IndexPage } from "pages";
import { LivePage } from "pages/live";

export const Root = () => {
	return (
		<Routes>
			<Route path="/" element={<IndexPage />} />
			<Route path="/live" element={<LivePage />} />
		</Routes>
	);
};
