// resources/js/types/index.ts

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};

export interface Client {
    id: number;
    name: string;
    nif: string;
    business_name: string;
    email: string | null;
    phone: string | null;
    address: string | null;
    created_at: string;
    updated_at: string;
    photo_url?: string | null;
    rentals?: Rental[];
}

export interface Category {
    id: number;
    name: string;
    slug: string;
}

export interface Product {
    id: number;
    name: string;
    sku: string;
    description: string | null;
    category_id: number;
    tracking_type: 'BULK' | 'SERIALIZED';
    stock_quantity: number;
    replacement_value: number;
    is_active: boolean;
    category?: Category;
    assets?: Asset[];
    assets_count?: number;
    image_url?: string | null;
}

export interface Asset {
    id: number;
    product_id: number;
    serial_number: string;
    status: 'Disponível' | 'Alugado' | 'Em Manutenção' | 'Perdido';
    notes: string | null;
    created_at: string;
}

export interface Rental {
    id: number;
    client_id: number;
    rental_date: string;
    expected_return_date: string;
    actual_return_date: string | null;
    status: string;
    client?: Client;
    rental_items?: RentalItem[];
}

export interface RentalItem {
    id: number;
    product_id: number;
    asset_id: number | null;
    quantity: number | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}


export interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        links: PaginationLink[];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}


export interface RentalItem {
    id: number;
    product_id: number;
    asset_id: number | null;
    quantity_rented: number | null;
    quantity_returned: number;
    quantity_damaged: number;
    quantity_lost: number;
    product: Product;
    asset?: Asset;
}
