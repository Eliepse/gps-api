import Layout from "components/layout/layout";
import { useSelector } from "react-redux";
import { getTrackers } from "store/slices/trackersSlice";

export function IndexPage() {
	const trackers = useSelector(getTrackers);
	const metadata = useSelector(({ trackers }) => trackers.metadata);

	return (
		<Layout>
			<div className="m-8">
				<h2 className="text-lg mb-8">Trackers</h2>

				<table>
					<thead>
						<tr>
							<th className="text-left">Name</th>
							<th className="pl-4 text-left">Status</th>
							<th className="pl-4 text-left">Metadata</th>
						</tr>
					</thead>
					<tbody>
						{trackers.map((tracker) => (
							<tr className={!tracker.active && "text-gray-400"} key={tracker.uid}>
								<td>{tracker.name}</td>
								<td className="pl-4 ">{tracker.active ? "Connected" : "offline"}</td>
								<td className="pl-4 ">
									{tracker.active ? (
										<>
											<span>{metadata[tracker.uid]?.satellites?.visible}</span>
											<span>({metadata[tracker.uid]?.satellites?.active})</span>
										</>
									) : null}
								</td>
							</tr>
						))}
					</tbody>
				</table>
			</div>
		</Layout>
	);
}
