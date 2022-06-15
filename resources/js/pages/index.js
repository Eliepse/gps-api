import Layout from "components/layout/layout";
import { useSelector } from "react-redux";
import { getTracker, getTrackerMetadata } from "store/slices/trackerSlice";
import clsx from "clsx";

export function IndexPage() {
	const user = useSelector((state) => state.user);
	const tracker = useSelector(getTracker);
	const metadata = useSelector(getTrackerMetadata);

	return (
		<Layout>
			<div className="m-8">
				<h2 className="text-lg mb-8">Trackers</h2>

				<p>
					Total distance travelled: {user?.stats?.totalTravelled ? (user?.stats?.totalTravelled / 1000).toFixed(2) : "/"} km
				</p>

				<table>
					<thead>
					<tr>
						<th className="text-left">Name</th>
						<th className="pl-4 text-left">Status</th>
						<th className="pl-4 text-left">Metadata</th>
					</tr>
					</thead>
					<tbody>
					<tr className={clsx(!tracker.active && "text-gray-400")} key={tracker.uid}>
						<td>{tracker.name}</td>
						<td className="pl-4 ">{tracker.active ? "Connected" : "offline"}</td>
						<td className="pl-4 ">
							{tracker.active ? (
								<>
									<span>{metadata?.satellites?.active}</span>
									<span>({metadata?.satellites?.visible})</span>
								</>
							) : null}
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</Layout>
	);
}
