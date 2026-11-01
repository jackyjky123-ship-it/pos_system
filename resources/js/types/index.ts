export type * from './auth';
export type * from './navigation';
export type * from './ui';

export interface Category {
    id: number;
    name: string;
    description: string | null;
}

export interface Product {
    id: number;
    name: string;
    description: string | null;
    price: string;
    category_id: number;
    stock_quantity: number;
    image?: string | null;
    is_active: boolean;
    category: Category;
}

export interface CartItem {
    product: Product;
    quantity: number;
}

export interface SaleItem {
    id: number;
    sale_id: number;
    product_id: number;
    product_name: string;
    unit_price: string;
    quantity: number;
    subtotal: string;
    product?: Product;
}

export interface Sale {
    id: number;
    user_id: number;
    total_amount: string;
    cash_tendered: string;
    cash_change: string;
    status: string;
    notes?: string | null;
    created_at: string;
    updated_at: string;
    items?: SaleItem[];
    user?: {
        id: number;
        name: string;
        email: string;
    };
}
