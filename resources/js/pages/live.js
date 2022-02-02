import Layout from "components/layout/layout";
import { MapContainer, Marker, Popup, TileLayer } from "react-leaflet";

export function LivePage() {
	return (
		<Layout>
			<div className="h-full">
				<MapContainer className="h-full" center={[48.81602, 2.30063]} zoom={19}>
					<TileLayer
						attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
						url="https://b.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png"
					/>
					<Marker position={[48.81602, 2.30063]}>
						<Popup>Home sweet home</Popup>
					</Marker>
				</MapContainer>
			</div>
		</Layout>
	);
}
