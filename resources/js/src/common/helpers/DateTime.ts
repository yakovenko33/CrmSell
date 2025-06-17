

const pad = function(number: number): string {
    return (number < 10) ? `0${number}` : String(number);
}

export const getLocalDate = function(dateTime: string): string {
    const parse = Date.parse(dateTime);
    if (isNaN(parse)) {
        return '';
    }
    const date = new Date(parse);
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

export const getLocalDateTime = function(dateTime: string): string {
    const parse = Date.parse(dateTime);
    if (isNaN(parse)) {
        return '';
    }
    const date = new Date(parse);
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

