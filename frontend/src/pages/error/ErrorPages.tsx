import { isRouteErrorResponse, Link, useRouteError } from 'react-router-dom';
import { Compass, RotateCw, TriangleAlert } from 'lucide-react';
import { Button } from '@/components/ui/Button';

function Shell({
    code,
    title,
    description,
    icon,
    children,
}: {
    code: string;
    title: string;
    description: string;
    icon: React.ReactNode;
    children?: React.ReactNode;
}) {
    return (
        <div className="grid min-h-dvh place-items-center bg-bg px-4">
            <div className="max-w-md text-center">
                <div className="mx-auto grid size-16 place-items-center rounded-full bg-primary-100 text-primary dark:bg-primary/20">
                    {icon}
                </div>
                <p className="mt-6 font-mono text-sm font-semibold text-fg-muted">{code}</p>
                <h1 className="mt-1 font-display text-2xl font-extrabold">{title}</h1>
                <p className="mt-2 text-sm text-fg-muted">{description}</p>
                <div className="mt-6 flex justify-center gap-2">{children}</div>
            </div>
        </div>
    );
}

export function NotFoundPage() {
    return (
        <Shell
            code="404"
            title="Halaman tidak ditemukan"
            description="Tautan yang kamu buka mungkin sudah dipindahkan atau tidak pernah ada."
            icon={<Compass className="size-7" />}
        >
            <Button to="/">Kembali ke beranda</Button>
            <Button to="/app/dashboard" variant="outline">
                Ke dashboard
            </Button>
        </Shell>
    );
}

export function ErrorBoundaryPage() {
    const error = useRouteError();

    const message = isRouteErrorResponse(error)
        ? `${error.status} ${error.statusText}`
        : error instanceof Error
          ? error.message
          : 'Kesalahan tidak diketahui.';

    return (
        <Shell
            code="Error"
            title="Ada yang tidak beres"
            description={message}
            icon={<TriangleAlert className="size-7" />}
        >
            <Button onClick={() => window.location.reload()} icon={<RotateCw className="size-4" />}>
                Muat ulang
            </Button>
            <Link to="/" className="inline-flex">
                <Button variant="outline">Beranda</Button>
            </Link>
        </Shell>
    );
}
