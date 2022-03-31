const p = Math.PI / 180; // Math.PI / 180

export function geoDistance([lat1, lon1], [lat2, lon2]) {
	const a =
		0.5 -
		Math.cos((lat2 - lat1) * p) / 2 +
		(Math.cos(lat1 * p) * Math.cos(lat2 * p) * (1 - Math.cos((lon2 - lon1) * p))) / 2;
	return 12742000 * Math.asin(Math.sqrt(a)); // 2 * R; R = 6371 km
}