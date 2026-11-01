import { Head, Link } from '@inertiajs/react';
import { Sale } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft, CheckCircle2, Printer, ShoppingCart } from 'lucide-react';

interface Props {
    sale: Sale;
}

export default function PosReceipt({ sale }: Props) {
    function handlePrint() {
        window.print();
    }

    const formattedDate = new Date(sale.created_at).toLocaleString();

    return (
        <>
            <Head title={`Receipt #${sale.id}`} />
            <div className="min-h-screen bg-muted/40 py-8 px-4 flex flex-col items-center justify-center print:bg-white print:p-0">
                {/* Actions Bar (hidden when printing) */}
                <div className="w-full max-w-md mb-4 flex items-center justify-between print:hidden">
                    <Link
                        href="/pos"
                        className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back to POS
                    </Link>
                    <div className="flex gap-2">
                        <Button variant="outline" size="sm" onClick={handlePrint}>
                            <Printer className="mr-2 h-4 w-4" />
                            Print
                        </Button>
                        <Button size="sm" asChild>
                            <Link href="/pos">
                                <ShoppingCart className="mr-2 h-4 w-4" />
                                New Sale
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Receipt Card */}
                <div className="w-full max-w-md bg-card border rounded-xl shadow-sm p-6 print:border-none print:shadow-none print:p-0">
                    {/* Header */}
                    <div className="text-center border-b pb-4">
                        <div className="flex justify-center mb-2 print:hidden">
                            <CheckCircle2 className="h-10 w-10 text-green-500" />
                        </div>
                        <h1 className="text-xl font-bold tracking-tight">Point of Sale</h1>
                        <p className="text-xs text-muted-foreground mt-1">Transaction Receipt</p>
                    </div>

                    {/* Meta info */}
                    <div className="py-3 text-xs border-b space-y-1 text-muted-foreground">
                        <div className="flex justify-between">
                            <span>Receipt #:</span>
                            <span className="font-mono font-medium text-foreground">{sale.id}</span>
                        </div>
                        <div className="flex justify-between">
                            <span>Date:</span>
                            <span className="text-foreground">{formattedDate}</span>
                        </div>
                        {sale.user && (
                            <div className="flex justify-between">
                                <span>Cashier:</span>
                                <span className="text-foreground">{sale.user.name}</span>
                            </div>
                        )}
                        <div className="flex justify-between">
                            <span>Status:</span>
                            <span className="capitalize font-medium text-foreground">{sale.status}</span>
                        </div>
                    </div>

                    {/* Items table */}
                    <div className="py-4 border-b">
                        <div className="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2 grid grid-cols-12">
                            <span className="col-span-6">Item</span>
                            <span className="col-span-2 text-center">Qty</span>
                            <span className="col-span-2 text-right">Price</span>
                            <span className="col-span-2 text-right">Total</span>
                        </div>
                        <div className="space-y-2">
                            {sale.items?.map((item) => (
                                <div key={item.id} className="text-sm grid grid-cols-12 items-center">
                                    <span className="col-span-6 font-medium truncate">{item.product_name}</span>
                                    <span className="col-span-2 text-center text-muted-foreground">{item.quantity}</span>
                                    <span className="col-span-2 text-right text-muted-foreground">${item.unit_price}</span>
                                    <span className="col-span-2 text-right font-medium">${item.subtotal}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Totals */}
                    <div className="pt-4 space-y-2 text-sm">
                        <div className="flex justify-between text-base font-bold">
                            <span>Total Amount</span>
                            <span>${sale.total_amount}</span>
                        </div>
                        <div className="flex justify-between text-muted-foreground">
                            <span>Cash Tendered</span>
                            <span>${sale.cash_tendered}</span>
                        </div>
                        <div className="flex justify-between font-semibold text-green-600 dark:text-green-400">
                            <span>Change</span>
                            <span>${sale.cash_change}</span>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="mt-8 pt-4 border-t text-center text-xs text-muted-foreground">
                        <p>Thank you for your purchase!</p>
                    </div>
                </div>
            </div>
        </>
    );
}
