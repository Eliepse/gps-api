import { useEffect, useRef, useState } from "react";
import Layout from "components/layout/layout";
import { Circle, MapContainer, Polyline, TileLayer } from "react-leaflet";
import styles from "./live.module.scss";
import { batch, useDispatch, useSelector } from "react-redux";
import clsx from "clsx";
import dayjs from "dayjs";
import { api } from "lib/api/axios";
import { emptyArray } from "lib/supports/array";
import {
	addCoordinates,
	startTrace as startTraceSlice,
	stopTrace as stopTraceSlice,
	updateLength
} from "store/slices/traceSlice";
import { getTracker, getTrackerMetadata } from "store/slices/trackerSlice";
import { Mercure } from "lib/EventSourceManager";

export function LivePage() {
	const map = useRef();
	const dispatch = useDispatch();
	const [loading, setLoading] = useState(false);
	const [autoPan, setAutoPan] = useState(true);

	const tracker = useSelector(getTracker);
	const trackerMetadata = useSelector(getTrackerMetadata);
	const isTrackerOnline = tracker?.active;
	const trace = useSelector((state) => state.trace);

	const hasTrace = Boolean(trace);
	const isTracking = trace?.status === "recording";
	const isWaitingGPSUpdate = isTrackerOnline && !trackerMetadata;

	/*
	 | ************************
	 | Actions
	 | ************************
	 */

	function startTrace() {
		if (!tracker.uid) {
			return;
		}

		setLoading(true);

		api
			.post("/trace", { tracker_uid: tracker.uid })
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

		if (!trackerMetadata?.coordinate || trackerMetadata.coordinate.length !== 2) {
			return;
		}

		map.current.panTo(trackerMetadata.coordinate);
	}, [autoPan, trackerMetadata]);

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
								<br />
								{trace.averageSpeed && `${trace.averageSpeed.toFixed(1)} km/h`}
							</Display>
						)}

						<Display className="px-4 flex items-center justify-center text-slate-400 mt-4">
							<span className="mr-2">
								<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
									<path strokeLinecap="round" strokeLinejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
								</svg>
							</span>
							{trackerMetadata?.satellites?.active > 0 ? (
								<span>{trackerMetadata.satellites?.active}&nbsp;({trackerMetadata.satellites?.visible})</span>
							) : "---"}
						</Display>
					</div>
				</div>

				<MapContainer
					zoomControl={false}
					attributionControl={false}
					className="h-full z-0"
					center={tracker.lastCoordinate || [48.81602, 2.30063]}
					zoom={18}
					whenCreated={handleCreated}
				>
					<TileLayer url="https://{s}.tile-cyclosm.openstreetmap.fr/[cyclosm|cyclosm-lite]/{z}/{x}/{y}.png" />
					{trackerMetadata?.coordinate?.length === 2 && (
						<Circle center={trackerMetadata.coordinate} radius={(trackerMetadata?.precision || 4) * 2.5} color="#fb923c" />
					)}
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
