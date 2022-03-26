import Layout from "components/layout/layout";
import { useSelector } from "react-redux";

export function IndexPage() {
	const trackers = useSelector((store) => store.trackers);

	return (
		<Layout>
			<div className="m-8">
				<h2 className="text-lg mb-8">Trackers</h2>

				<table>
					<thead>
						<tr>
							<th className="text-left">Name</th>
							<th className="pl-4 text-left">Status</th>
						</tr>
					</thead>
					<tbody>
						{trackers.map((tracker) => (
							<tr className={!tracker.active && "text-gray-400"} key={tracker.uid}>
								<td>{tracker.name}</td>
								<td className="pl-4 ">{tracker.active ? "Connected" : "offline"}</td>
							</tr>
						))}
					</tbody>
				</table>
			</div>
		</Layout>
	);
}
