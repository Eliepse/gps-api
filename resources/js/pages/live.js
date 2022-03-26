import { useEffect, useMemo, useRef, useState } from "react";
import Layout from "components/layout/layout";
import { Circle, MapContainer, Polyline, TileLayer } from "react-leaflet";
import styles from "./live.module.scss";
import { useSelector } from "react-redux";
import { getUserId } from "store/slices/userSlice";
import clsx from "clsx";
import { useStateMachine } from "lib/useStateMachine";
import { liveMachine } from "lib/stateMachines/liveMachine";
import dayjs from "dayjs";
import { api } from "lib/api/axios";

export function LivePage() {
	const map = useRef();
	const userId = useSelector(getUserId);
	const [lastGPS, setLastGPS] = useState();
	const { isState, can, send, context, state } = useStateMachine(liveMachine, {}, true);
	const { trace } = context;
	const hasDataToDisplay = Boolean(trace);

	useEffect(() => {
		api
			.get("/recoverData")
			.then((res) => {
				console.debug(res);
				if (res.status === 204) {
					send("freshStart");
					return;
				}

				send("alreadyRecording", res.data);
			})
			.catch(console.error);
	}, []);

	useEffect(() => {
		return () => {};
	}, [state !== "init"]);

	/*
	 |------------------
	 | Render
	 |------------------
	 */

	const path = useMemo(() => {
		return (
			context.path?.map((coord) => {
				const [lon, lat] = coord.location.coordinates;
				return [lat, lon];
			}) || []
		);
	}, [context.path]);
	const stats = useMemo(() => {
		const p = Math.PI / 180; // Math.PI / 180

		function calcDistance([lat1, lon1], [lat2, lon2]) {
			const a =
				0.5 -
				Math.cos((lat2 - lat1) * p) / 2 +
				(Math.cos(lat1 * p) * Math.cos(lat2 * p) * (1 - Math.cos((lon2 - lon1) * p))) / 2;
			return 12742000 * Math.asin(Math.sqrt(a)); // 2 * R; R = 6371 km
		}

		let i = 0,
			m = path.length,
			distance = 0;

		if (path.length >= 2) {
			for (; i < m - 1; i++) {
				distance += calcDistance(path[i], path[i + 1]);
			}
		}

		return {
			distance,
		};
	}, [path]);

	return (
		<Layout>
			<div className={styles.viewport}>
				<div className={styles.overlay}>
					<div className={styles.overlayHeader}>
						{(can("startTracking") || isState("preRecording")) && (
							<ActionButton type="primary" onClick={() => send("startTracking")} loading={isState("preRecording")}>
								{isState("preRecording") ? "Starting..." : "Start tracking"}
							</ActionButton>
						)}
						{(can("stopTracking") || isState("postRecording")) && (
							<ActionButton type="danger" onClick={() => send("stopTracking")} loading={isState("postRecording")}>
								{isState("postRecording") ? "Stopping..." : "Stop tracking"}
							</ActionButton>
						)}
					</div>
					<div className={styles.overlayBody} />
					<div className={clsx(styles.overlayFooter, "pb-16")}>
						{isState("waitLocation") && (
							<Display className="px-4 flex items-center justify-center text-slate-400">
								<span className="animate-spin text-2xl">🕛</span> Waiting GPS...
							</Display>
						)}

						{isState("offline") && (
							<Display className="px-4 flex items-center justify-center text-slate-400">Tracker offline</Display>
						)}

						{hasDataToDisplay > 0 && (
							<Display className="px-4 flex items-center">
								<Timer start={trace.started_at} end={trace.finished_at} />
								<br />
								{(stats.distance / 1000).toFixed(3)} km
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
					whenCreated={(m) => (map.current = m)}
				>
					<TileLayer url="https://b.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png" />
					{lastGPS && <Circle center={lastGPS.coordinates} radius={(lastGPS.precision || 2) * 2.5} />}
					{path.length > 0 && <Polyline positions={path} color="#fb923c" />}
				</MapContainer>
			</div>
		</Layout>
	);
}

const Display = ({ className, ...rest }) => {
	return <div className={clsx(styles.display, className)} {...rest} />;
};

const TYPES_CLASSES = {
	primary: styles.primary,
	danger: styles.danger,
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
