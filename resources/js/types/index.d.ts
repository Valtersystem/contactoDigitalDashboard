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
