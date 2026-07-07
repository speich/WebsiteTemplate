/**
 * A Module providing helper methods to work with numbers.
 */
export const numberUtil = {

    /**
     * Test if a value is numeric.
     * @param value
     * @return {Boolean}
     */
    isNumeric: function (value) {
        // taken from jquery
        return !isNaN(parseFloat(value)) && isFinite(value);
    },

    /**
     * Rounds to a specified number of decimal places.
     * @param num
     * @param numPlaces
     * @return {Number}
     */
    roundTo: function (num, numPlaces) {
        let powered, rounded;

        powered = Math.pow(10, numPlaces);
        rounded = Math.round(num * powered) / powered;

        return rounded;
    },

    /**
     * Rounds to a specified number of place values.
     * @param num
     * @param numPlaces
     * @return {Number}
     */
    roundToPlaces: function (num, numPlaces) {
        let powered, rounded;

        powered = Math.pow(10, numPlaces);
        rounded = Math.round(num / powered) * powered;

        return rounded;
    },

    /**
     * Returns an object with the number of places, decimal values, and leading decimal zeros.
     * @param {number|string} num
     * @return {{places: number, decimals: number, decLeadingZeros: number}}
     */
    getPlaces: function(num) {
        // 1. Validate input to prevent NaN or Infinity errors
        const n = Number(num);
        if (!Number.isFinite(n)) {
            return { places: 0, decimals: 0, decLeadingZeros: 0 };
        }

        // 2. Convert the absolute value to a string
        // Note: Extremely large/small numbers (e.g., 1e-7) will parse as scientific notation strings.
        const strNum = Math.abs(n).toString();

        // 3. Split the string into Integer and Decimal parts
        const parts = strNum.split('.');
        const intPart = parts[0];
        const decPart = parts[1] || ''; // Default to empty string if no decimal exists

        // 4. Calculate integer places (treating "0" as having 0 places, per original logic)
        const places = intPart === '0' ? 0 : intPart.length;

        // 5. Calculate total decimal places
        const decimals = decPart.length;

        // 6. Use regex to efficiently find consecutive leading zeros in the decimal part
        const leadingZerosMatch = decPart.match(/^0+/);
        const decLeadingZeros = leadingZerosMatch ? leadingZerosMatch[0].length : 0;

        return {
            places,
            decimals,
            decLeadingZeros
        };
    }
};