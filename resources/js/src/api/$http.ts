
import axios from "axios";

export interface ServerResponseId {
    id: string;
}

export interface CustomError {
    field: string;
    message : string;
}

export interface ServerResponse<R> {
    status: string;
    data: R;
    errors: CustomError[];
}

function patch<T, R>(url: string, body: T): Promise<ServerResponse<R>> {
    return axios.patch(`/api/v1/${url}`, body, {
        headers: {
            'Content-Type': 'application/json',
        }
    }).then((response) => {
        return response.data;
    });
}

export const $http = {
    // get,
    // post,
    patch
};
