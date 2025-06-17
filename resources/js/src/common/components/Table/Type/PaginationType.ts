
export interface PaginationType {
    pages: {
        total_pages: number;
        current_page: number;
        first_page: number;
        second_page: number|null;
        third_page: number|null;
        previous_page: number|null;
        next_page: number|null;
        last_page: number;
    },
    records: {
        all: number;
        from: number;
        to: number;
    }
}
