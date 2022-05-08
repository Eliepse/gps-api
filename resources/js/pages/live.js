import { useEffect, useMemo, useRef, useState } from "react";
import Layout from "components/layout/layout";
import { Circle, MapContainer, Polyline, TileLayer } from "react-leaflet";
import styles from "./live.module.scss";
import { batch, useDispatch, useSelector } from "react-redux";
import clsx from "clsx";
import dayjs from "dayjs";
import { api } from "lib/api/axios";
import { arrLast, emptyArray } from "lib/supports/array";
import {
	addCoordinates,
	startTrace as startTraceSlice,
	stopTrace as stopTraceSlice,
	updateLength
} from "store/slices/traceSlice";
import { hasOnlineTracker } from "store/slices/trackersSlice";
import { Mercure } from "lib/EventSourceManager";

export function LivePage() {
	const map = useRef();
	const dispatch = useDispatch();
	const [loading, setLoading] = useState(false);
	const [autoPan, setAutoPan] = useState(true);

	const trackers = useSelector((state) => state.trackers.trackers);
	const trackersMetadata = useSelector((state) => state.trackers.metadata);
	const isTrackerOnline = useSelector(hasOnlineTracker);
	const trace = useSelector((state) => state.trace);
	const lastMetadata = arrLast(Object.values(trackersMetadata));

	const hasTrace = Boolean(trace);
	const isTracking = trace?.status === "recording";
	const isWaitingGPSUpdate = isTrackerOnline && !lastMetadata;

	/*
	 | ************************
	 | Actions
	 | ************************
	 */

	function startTrace() {
		const trackersArray = Object.values(trackers);
		if (trackersArray.length === 0) {
			return;
		}

		setLoading(true);

		api
			.post("/trace", { tracker_uid: trackersArray[0].uid })
			.then(({ data }) => dispatch(startTraceSlice(data)))
			.catch(console.error)
			.finally(() => setLoading(false));
	}

	function stopTrace() {
		if (!isTracking) {
			return;
		}

		setLoading(true);

		api
			.post(`/trace/${trace.uid}/stop`)
			.then(({ data }) => dispatch(stopTraceSlice(data)))
			.catch(console.error)
			.finally(() => setLoading(false));
	}

	useEffect(() => {
		//api
		//	.get("/recoverData")
		//	.then((res) => {
		//		console.debug(res);
		//		if (res.status === 204) {
		//			send("freshStart");
		//			return;
		//		}
		//
		//		send("alreadyRecording", res.data);
		//	})
		//	.catch(console.error);
	}, []);

	/*
	 | ************************
	 | Events handlers
	 | ************************
	 */

	function handleCreated(m) {
		map.current = m;
		m.on("dragstart", () => setAutoPan(false));
	}

	useEffect(() => {
		if (!autoPan || !map.current) {
			return;
		}

		if (!lastMetadata?.coordinate) {
			return;
		}

		map.current.panTo(lastMetadata?.coordinate || []);
	}, [autoPan, lastMetadata]);

	useEffect(() => {
		function updateTraceCoordinates(data) {
			batch(() => {
				dispatch(addCoordinates(data?.coordinates || []));
				dispatch(updateLength(data.length));
			});
		}

		Mercure.addMessageListener("App\\Events\\TraceCoordinatesUpdated", updateTraceCoordinates);
		return () => Mercure.removeMessageListener(updateTraceCoordinates);
	}, [trace?.uid]);

	/*
	 | ************************
	 | Render
	 | ************************
	 */
	const stats = useMemo(() => {
		//let i = 0,
		//	m = path.length,
		//	distance = 0;
		//
		//if (path.length >= 2) {
		//	for (; i < m - 1; i++) {
		//		distance += calcDistance(path[i], path[i + 1]);
		//	}
		//}

		return {
			distance: 0
		};
	}, []);

	return (
		<Layout>
			<div className={styles.viewport}>
				<div className={styles.overlay}>
					{/*
					 | ************************
					 | Header
					 | ************************
					 */}
					<div className={styles.overlayHeader}>
						{isTrackerOnline && !isTracking && (
							<ActionButton type="primary" onClick={startTrace} loading={loading}>
								Start tracking
							</ActionButton>
						)}
						{isTracking && (
							<ActionButton type="danger" onClick={stopTrace} loading={loading}>
								Stop tracking
							</ActionButton>
						)}
					</div>

					{/*
					 | ************************
					 | Body
					 | ************************
					 */}
					<div className={styles.overlayBody}>
						<div className="m-2 bg-white p-1 text-xs rounded inline-block">
							{Object.values(trackersMetadata).map(({ satellites }) => (
								<>satellites: {satellites?.active}&nbsp;({satellites?.visible})<br /></>
							))}
						</div>
					</div>

					{/*
					 | ************************
					 | Footer
					 | ************************
					 */}
					<div className={clsx(styles.overlayFooter, "pb-16")}>
						{!isTrackerOnline && (
							<Display className="px-4 flex items-center justify-center text-slate-400">Tracker offline</Display>
						)}

						{hasTrace && (
							<Display signalLost={isWaitingGPSUpdate} className="px-4 flex items-center">
								<Timer start={trace.started_at} end={trace.finished_at} />
								<br />
								{(trace.length / 1000).toFixed(3)} km
							</Display>
						)}
					</div>
				</div>

				<MapContainer
					zoomControl={false}
					attributionControl={false}
					className="h-full z-0"
					center={[48.81602, 2.30063]}
					zoom={19}
					whenCreated={handleCreated}
				>
					<TileLayer url="https://b.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png" />
					{Object.entries(trackersMetadata).map(([uid, meta]) => (
						meta?.coordinate && meta?.coordinate?.length > 0 &&
						<Circle key={uid} center={meta.coordinate} radius={meta.precision * 2.5} color="#fb923c" />
					))}
					{hasTrace && <Polyline positions={trace?.coordinates || emptyArray} color="#fb923c" />}
				</MapContainer>
			</div>
		</Layout>
	);
}

const Display = ({ signalLost = false, className, ...rest }) => {
	return <div className={clsx(styles.display, signalLost && styles.noSignal, className)} {...rest} />;
};

const TYPES_CLASSES = {
	primary: styles.primary,
	danger: styles.danger
};

const ActionButton = ({ type, loading = false, className, ...rest }) => {
	return (
		<div className={clsx(styles.actionBtn, TYPES_CLASSES[type], className)} {...rest}>
			{loading && <span className="animate-spin text-lg">🕛</span>}
			{rest.children}
		</div>
	);
};

const Timer = ({ start, end }) => {
	const [, update] = useState({});

	useEffect(() => {
		let interval;

		if (!end) {
			interval = setInterval(() => update({}), 1000);
		}

		return () => clearInterval(interval);
	}, [end]);

	if (!end) {
		return dayjs.duration(dayjs().diff(start)).format("H:mm:ss");
	}

	return dayjs.duration(dayjs(end).diff(start)).format("H:mm:ss");
};
