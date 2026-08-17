import type { Order } from '@/types';
import { http, unwrap } from './client';

export type PaymentMode = 'tripay' | 'simulated' | null;

/**
 * Course checkout / QRIS payment — always calls the real API, same as
 * `learningService`. There's no mock counterpart: this domain only makes
 * sense against real enrollment/order state.
 */
export const checkoutService = {
    /**
     * Free modules (or ones already paid for) enroll immediately.
     * Paid ones return a Tripay QRIS transaction (`paymentMode: 'tripay'`) or,
     * if Tripay isn't configured yet, a simulated fallback order.
     */
    checkout: async (
        moduleSlug: string,
    ): Promise<{ requiresPayment: boolean; enrolled: boolean; paymentMode: PaymentMode; order: Order | null }> => {
        const data = unwrap<{
            requires_payment: boolean;
            enrolled: boolean;
            payment_mode: PaymentMode;
            order: Order | null;
        }>((await http.post(`/learning/modules/${moduleSlug}/checkout`)).data);

        return {
            requiresPayment: data.requires_payment,
            enrolled: data.enrolled,
            paymentMode: data.payment_mode,
            order: data.order,
        };
    },

    /** Dev/demo stand-in for Tripay — rejected for orders with a real gateway transaction. */
    simulatePayment: async (orderId: number): Promise<{ order: Order; enrolled: boolean }> =>
        unwrap((await http.post(`/orders/${orderId}/simulate-payment`)).data),

    /** "Cek Status Pembayaran" — actively asks Tripay instead of waiting for a webhook. */
    checkStatus: async (orderId: number): Promise<{ order: Order; enrolled: boolean }> =>
        unwrap((await http.get(`/orders/${orderId}/check-status`)).data),
};
