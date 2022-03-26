import { Route, Routes } from "react-router-dom";
import { IndexPage } from "pages";
import { LivePage } from "pages/live";
import { useEffect } from "react";
import { batch, useDispatch, useSelector } from "react-redux";
import { hydrateUser } from "store/slices/userSlice";
import { api } from "lib/api/axios";
import { addTracker, hydrateTrackers, removeTracker, updateTrackerMetadata } from "store/slices/trackersSlice";
import { Mercure } from "lib/EventSourceManager";

export const Root = () => {
	const dispatch = useDispatch();
	const userUid = useSelector((store) => store.user.uuid);

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
						`/.well-known/mercure/subscriptions/%2Fuser%2F${id}{/subscriber}`,
					];
					Mercure.connect();
				});
			})
			.catch(console.error);

		return () => Mercure.disconnect();
	}, [dispatch]);

	useEffect(() => {
		function updateActiveTrackers(data) {
			const {
				topic,
				active,
				payload: { type },
			} = data;

			if (!`^/\/user\/${userUid}\/trackers\/[0-9]+$/`.test(topic)) {
				return;
			}

			if (type !== "\\App\\Models\\Tracker") {
				return;
			}

			if (active) {
				dispatch(addTracker(data));
			} else {
				dispatch(removeTracker(data));
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
	}, [dispatch, userUid]);

	return (
		<Routes>
			<Route path="/" element={<IndexPage />} />
			<Route path="/live" element={<LivePage />} />
		</Routes>
	);
};
