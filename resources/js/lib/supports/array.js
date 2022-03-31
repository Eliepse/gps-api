export const emptyArray = [];

export function arrWrap(value) {
	return Array.isArray(value) ? value : [value];
}

export function arrLast(array) {
	if (!Array.isArray(array) || array.length === 0) {
		return undefined;
	}

	return array[array.length - 1];
}
