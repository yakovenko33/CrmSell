
export interface FilterType {
    order_date_from: string;
    order_date_to: string;
    vendor_code: string;
    goods_name: string;
    vendor_code_value: string;
    goods_name_value: string;
    goods_id: string;
    defect: string;
    provider_start: string;
    manager: string;
    status: Array<string>;
    date_check_from: string;
    date_check_to: string;
    comment: string;
    order_number: string;
    remainder: boolean;
}
