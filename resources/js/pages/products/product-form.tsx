import { Category, Product } from "@/types";
import { useForm } from "@inertiajs/react";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from "@/components/ui/label";
import InputError from '@/components/input-error';
import { FormEvent } from "react";
import { toast } from "sonner";

interface Props {
    categories: Category[];
    product?: Product | null;
    onClose: () => void;
}

export default function ProductForm({ categories, product, onClose }: Props) {
    const { data, setData, post, processing, errors, reset, transform } = useForm({
        name:           product?.name ?? '',
        category_id:    product?.category_id?.toString() ?? '',
        description:    product?.description ?? '',
        price:          product?.price ?? '',
        stock_quantity: product?.stock_quantity?.toString() ?? '0',
        is_active:      product?.is_active ?? true,
        image:          null as File | null,
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        const opts = {
            forceFormData: true,
            onSuccess: () => {
                toast.success(product ? 'Product updated.' : 'Product created.');
                reset();
                onClose();
            },
            onError: () => {
                toast.error('Failed to save product. Please check the form errors.');
            },
        };

        if (product) {
            transform((currentData) => ({
                ...currentData,
                _method: 'put',
            }));
            post(`/products/${product.id}`, opts);
        } else {
            transform((currentData) => ({
                ...currentData,
            }));
            post('/products', opts);
        }
    }

    return (
        <Dialog open onOpenChange={onClose}>
            <DialogContent className="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{product ? 'Edit Product' : 'Add Product'}</DialogTitle>
                </DialogHeader>

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <Label htmlFor="name">Name</Label>
                        <Input id="name" value={data.name} onChange={e => setData('name', e.target.value)} />
                        <InputError message={errors.name} />
                    </div>

                    <div>
                        <Label>Category</Label>
                        <Select value={data.category_id} onValueChange={v => setData('category_id', v)}>
                            <SelectTrigger><SelectValue placeholder="Select category" /></SelectTrigger>
                            <SelectContent>
                                {categories.map(c => (
                                    <SelectItem key={c.id} value={c.id.toString()}>{c.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.category_id} />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="price">Price ($)</Label>
                            <Input
                                id="price"
                                type="number"
                                step="0.01"
                                min="0"
                                value={data.price}
                                onChange={e => setData('price', e.target.value)}
                            />
                            <InputError message={errors.price} />
                        </div>
                        <div>
                            <Label htmlFor="stock_quantity">Stock</Label>
                            <Input
                                id="stock_quantity"
                                type="number"
                                min="0"
                                value={data.stock_quantity}
                                onChange={e => setData('stock_quantity', e.target.value)}
                            />
                            <InputError message={errors.stock_quantity} />
                        </div>
                    </div>

                    <div>
                        <Label htmlFor="description">Description</Label>
                        <Input
                            id="description"
                            value={data.description}
                            onChange={e => setData('description', e.target.value)}
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div>
                        <Label htmlFor="image">Image (optional)</Label>
                        <Input
                            id="image"
                            type="file"
                            accept="image/*"
                            onChange={e => setData('image', e.target.files?.[0] ?? null)}
                        />
                        {product?.image && !data.image && (
                            <p className="mt-1 text-xs text-muted-foreground">
                                Current image: <span className="font-mono">{product.image}</span>
                            </p>
                        )}
                        <InputError message={errors.image} />
                    </div>

                    <div className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="is_active"
                            checked={data.is_active}
                            onChange={e => setData('is_active', e.target.checked)}
                        />
                        <Label htmlFor="is_active">Active (visible in POS)</Label>
                    </div>

                    <div className="flex justify-end gap-2 pt-2">
                        <Button type="button" variant="outline" onClick={onClose}>Cancel</Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : product ? 'Update' : 'Create'}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
