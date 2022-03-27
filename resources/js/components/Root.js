import { Route, Routes } from "react-router-dom";
import { IndexPage } from "pages";
import { LivePage } from "pages/live";
import { useEffect } from "react";
import { batch, useDispatch, useSelector } from "react-redux";
import { hydrateUser } from "store/slices/userSlice";
import { api } from "lib/api/axios";
import { connectTracker, disconnectTracker, hydrateTrackers, updateTrackerMetadata } from "store/slices/trackersSlice";
import { Mercure } from "lib/EventSourceManager";

export const Root = () => {
	const dispatch = useDispatch();
	const userId = useSelector((store) => store.user.id);

	useEffect(() => {
		api
			.get("/me")
			.then((res) => {
				batch(() => {
					const { id } = res.data.user;
					dispatch(hydrateUser(res.data.user));
					dispatch(hydrateTrackers(res.data.trackers));
					Mercure.topics = [
						`/user/${id}`,
						`/user/${id}/trace/{trace}`,
						`/user/${id}/trackers/{tracker}`,
						`/.well-known/mercure/subscriptions/%2Fuser%2F${id}%2Ftrackers%2F{tracker}{/subscriber}`,
					];
					Mercure.connect();
				});
			})
			.catch(console.error);

		return () => Mercure.disconnect();
	}, [dispatch]);

	useEffect(() => {
		const regex = new RegExp(`^\/user\/${userId}\/trackers\/[0-9a-zA-Z-]+$`);

		function updateActiveTrackers(data) {
			const { payload } = data;

			if (!regex.test(data.topic)) {
				return;
			}

			if (payload.type !== "App\\Models\\Tracker") {
				return;
			}

			if (data.active) {
				dispatch(connectTracker(payload));
			} else {
				dispatch(disconnectTracker(payload));
			}
		}

		function handleTrackerMetadata(data) {
			dispatch(updateTrackerMetadata(data));
		}

		function debug(data, event) {
			console.debug(event, data);
		}

		Mercure.addPresenceListener(updateActiveTrackers);
		Mercure.addMessageListener("App\\Events\\TrackerMetadataUpdated", handleTrackerMetadata);
		return () => {
			Mercure.removePresenceListener(updateActiveTrackers);
			Mercure.removePresenceListener(handleTrackerMetadata);
			Mercure.removeMessageListener(debug);
		};
	}, [dispatch, userId]);

	return (
		<Routes>
			<Route path="/" element={<IndexPage />} />
			<Route path="/live" element={<LivePage />} />
		</Routes>
	);
};
