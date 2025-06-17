
import {PaginationType} from "../Type/PaginationType";

const pagination = {
    data() {
        return {
            pagination: {
                pages: {
                    total_pages: 0,
                    current_page: 1,
                    first_page: 0,
                    second_page: null,
                    third_page: null,
                    last_page: 0,
                    next_page: 0,
                    previous_page: 0,
                },
                records: {
                    all: 0,
                    from: 0,
                    to: 0
                }
            } as PaginationType,
        };
    }
} ;

export default pagination;
