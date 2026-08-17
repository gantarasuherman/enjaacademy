import { useState } from 'react';
import { ExternalLink, RefreshCw, Wallet } from 'lucide-react';
import { checkoutService, type PaymentMode } from '@/services/api';
import { useUiStore } from '@/store/uiStore';
import { formatCurrency } from '@/utils/format';
import type { LearningModule, Order } from '@/types';
import { Modal } from '@/components/ui/Modal';
import { Button } from '@/components/ui/Button';

/**
 * "Pilih & Ambil Kursus" confirmation — free courses enroll on confirm, paid
 * ones move to a payment step. Payment is real QRIS via Tripay when
 * configured (`paymentMode: 'tripay'`); otherwise a simulated fallback
 * button (`paymentMode: 'simulated'`) — see `CheckoutService::checkout()`.
 *
 * Tripay's webhook can't reach an unexposed local server, so "Cek Status
 * Pembayaran" actively polls Tripay on click rather than only waiting for one.
 */
export function CourseCheckoutModal({
    module,
    open,
    onClose,
    onEnrolled,
}: {
    module: LearningModule | null;
    open: boolean;
    onClose: () => void;
    onEnrolled: () => void;
}) {
    const toast = useUiStore((state) => state.toast);
    const [step, setStep] = useState<'confirm' | 'paying'>('confirm');
    const [order, setOrder] = useState<Order | null>(null);
    const [paymentMode, setPaymentMode] = useState<PaymentMode>(null);
    const [submitting, setSubmitting] = useState(false);

    function handleClose() {
        onClose();
        // Reset after the close animation has room to play out.
        setTimeout(() => {
            setStep('confirm');
            setOrder(null);
            setPaymentMode(null);
        }, 200);
    }

    async function handleConfirm() {
        if (!module) return;

        setSubmitting(true);
        try {
            const result = await checkoutService.checkout(module.slug);

            if (result.requiresPayment && result.order) {
                setOrder(result.order);
                setPaymentMode(result.paymentMode);
                setStep('paying');
            } else {
                toast(`Kursus "${module.name}" berhasil diambil!`, 'success');
                onEnrolled();
            }
        } catch {
            toast('Gagal memproses pengambilan kursus. Coba lagi.', 'danger');
        } finally {
            setSubmitting(false);
        }
    }

    async function handleSimulatePayment() {
        if (!order) return;

        setSubmitting(true);
        try {
            await checkoutService.simulatePayment(order.id);
            toast('Pembayaran berhasil — kursus sudah masuk ke Kursus Saya!', 'success');
            onEnrolled();
        } catch {
            toast('Gagal memproses pembayaran. Coba lagi.', 'danger');
        } finally {
            setSubmitting(false);
        }
    }

    async function handleCheckStatus() {
        if (!order) return;

        setSubmitting(true);
        try {
            const result = await checkoutService.checkStatus(order.id);

            if (result.order.status === 'paid') {
                toast('Pembayaran berhasil — kursus sudah masuk ke Kursus Saya!', 'success');
                onEnrolled();
            } else if (result.order.status === 'pending') {
                toast('Belum terbayar. Selesaikan pembayaran lalu coba cek lagi.', 'info');
            } else {
                toast('Pembayaran gagal atau kedaluwarsa — coba ambil kursus ini lagi.', 'danger');
                handleClose();
            }
        } catch {
            toast('Gagal memeriksa status pembayaran. Coba lagi.', 'danger');
        } finally {
            setSubmitting(false);
        }
    }

    if (!module) return null;

    return (
        <Modal
            open={open}
            onClose={handleClose}
            title={step === 'confirm' ? 'Konfirmasi Pengambilan Kursus' : 'Selesaikan Pembayaran'}
            footer={
                step === 'confirm' ? (
                    <>
                        <Button variant="outline" onClick={handleClose}>
                            Batal
                        </Button>
                        <Button onClick={handleConfirm} loading={submitting}>
                            {module.is_paid ? 'Lanjut ke Pembayaran' : 'Ambil Kursus Gratis'}
                        </Button>
                    </>
                ) : paymentMode === 'tripay' ? (
                    <>
                        <Button variant="outline" onClick={handleClose}>
                            Batal
                        </Button>
                        <Button onClick={handleCheckStatus} loading={submitting} icon={<RefreshCw className="size-4" />}>
                            Cek Status Pembayaran
                        </Button>
                    </>
                ) : (
                    <>
                        <Button variant="outline" onClick={handleClose}>
                            Batal
                        </Button>
                        <Button onClick={handleSimulatePayment} loading={submitting} icon={<Wallet className="size-4" />}>
                            Simulasikan Pembayaran Berhasil
                        </Button>
                    </>
                )
            }
        >
            {step === 'confirm' ? (
                <div className="space-y-3">
                    <p className="font-semibold">{module.name}</p>
                    <p className="text-sm text-fg-muted">
                        {module.is_paid
                            ? 'Kursus ini berbayar. Setelah konfirmasi, kamu akan diarahkan ke langkah pembayaran.'
                            : 'Kursus ini gratis. Konfirmasi untuk langsung mulai belajar.'}
                    </p>
                    <p className="font-display text-2xl font-extrabold">
                        {module.is_paid ? formatCurrency(module.price ?? 0) : 'Gratis'}
                    </p>
                </div>
            ) : paymentMode === 'tripay' ? (
                <div className="space-y-3">
                    {order?.qr_url && (
                        <div className="grid place-items-center rounded-lg bg-white p-4">
                            <img src={order.qr_url} alt="QRIS" className="size-48" />
                        </div>
                    )}

                    <p className="text-sm text-fg-muted">
                        Scan QRIS di atas pakai aplikasi bank/e-wallet mana pun, atau buka halaman pembayaran lengkap:
                    </p>

                    {order?.checkout_url && (
                        <a
                            href={order.checkout_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline"
                        >
                            Buka Halaman Pembayaran <ExternalLink className="size-3.5" />
                        </a>
                    )}

                    <div className="rounded-lg bg-surface-sunken p-4">
                        <div className="flex justify-between text-sm">
                            <span className="text-fg-muted">No. Transaksi</span>
                            <span className="font-mono">{order?.reference}</span>
                        </div>
                        <div className="mt-1 flex justify-between text-sm">
                            <span className="text-fg-muted">Jumlah</span>
                            <span className="font-semibold">{formatCurrency(order?.amount ?? 0)}</span>
                        </div>
                    </div>

                    <p className="text-xs text-fg-muted">
                        Sudah bayar? Klik "Cek Status Pembayaran" — pembayaran tidak terdeteksi otomatis di sini.
                    </p>
                </div>
            ) : (
                <div className="space-y-3">
                    <p className="text-sm text-fg-muted">
                        QRIS asli belum dikonfigurasi admin — ini simulasi pembayaran untuk keperluan demo.
                    </p>
                    <div className="rounded-lg bg-surface-sunken p-4">
                        <div className="flex justify-between text-sm">
                            <span className="text-fg-muted">No. Transaksi</span>
                            <span className="font-mono">{order?.reference}</span>
                        </div>
                        <div className="mt-1 flex justify-between text-sm">
                            <span className="text-fg-muted">Jumlah</span>
                            <span className="font-semibold">{formatCurrency(order?.amount ?? 0)}</span>
                        </div>
                    </div>
                </div>
            )}
        </Modal>
    );
}
