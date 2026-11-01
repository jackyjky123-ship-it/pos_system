export type * from './auth';
export type * from './navigation';
export type * from './ui';


export interface Category {
  id: number;
  name: string;
  description: string;
}

export interface Product {
  id: number;
  name: string;
  description: string;
  price: string;
  category_id: number;
  stock_quantity: number;
  image?: string | null;
  is_active: boolean;
  category: Category;
}

interface CartItem {
    product: Product;
    quantity: number;
    }
