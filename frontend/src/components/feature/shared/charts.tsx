import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Legend,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { formatDate } from '@/utils/format';

/**
 * Chart palette. Ordered so adjacent series stay distinguishable, and every
 * colour clears 3:1 contrast against both light and dark surfaces.
 */
export const CHART_COLORS = ['#02468B', '#EF8519', '#22C55E', '#0EA5E9', '#A855F7', '#EF4444'];

const AXIS = {
    stroke: 'var(--surface-muted)',
    fontSize: 11,
    tickLine: false,
    axisLine: false,
};

function ChartTooltip({ suffix = '' }: { suffix?: string }) {
    return (
        <Tooltip
            cursor={{ fill: 'var(--surface-sunken)', opacity: 0.5 }}
            contentStyle={{
                background: 'var(--surface)',
                border: '1px solid var(--surface-border)',
                borderRadius: 8,
                fontSize: 12,
                boxShadow: 'var(--shadow-pop)',
                color: 'var(--surface-ink)',
            }}
            formatter={(value: number | string) => [`${value}${suffix}`, '']}
        />
    );
}

export function ActivityAreaChart({
    data,
    dataKey = 'xp',
    suffix = ' XP',
    height = 240,
}: {
    data: { date: string; xp: number; minutes: number }[];
    dataKey?: 'xp' | 'minutes';
    suffix?: string;
    height?: number;
}) {
    return (
        <ResponsiveContainer width="100%" height={height}>
            <AreaChart data={data} margin={{ top: 8, right: 8, bottom: 0, left: -20 }}>
                <defs>
                    <linearGradient id="areaFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor={CHART_COLORS[1]} stopOpacity={0.35} />
                        <stop offset="100%" stopColor={CHART_COLORS[1]} stopOpacity={0.02} />
                    </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="var(--surface-border)" vertical={false} />
                <XAxis
                    dataKey="date"
                    {...AXIS}
                    tickFormatter={(value: string) => formatDate(value, 'd/M')}
                    interval="preserveStartEnd"
                    minTickGap={24}
                />
                <YAxis {...AXIS} width={44} />
                {ChartTooltip({ suffix })}
                <Area
                    type="monotone"
                    dataKey={dataKey}
                    stroke={CHART_COLORS[1]}
                    strokeWidth={2}
                    fill="url(#areaFill)"
                />
            </AreaChart>
        </ResponsiveContainer>
    );
}

export function ScoreBarChart({
    data,
    height = 240,
}: {
    data: { label: string; value: number }[];
    height?: number;
}) {
    return (
        <ResponsiveContainer width="100%" height={height}>
            <BarChart data={data} margin={{ top: 8, right: 8, bottom: 0, left: -20 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="var(--surface-border)" vertical={false} />
                <XAxis dataKey="label" {...AXIS} />
                <YAxis {...AXIS} width={44} domain={[0, 100]} />
                {ChartTooltip({ suffix: '%' })}
                <Bar dataKey="value" radius={[6, 6, 0, 0]}>
                    {data.map((entry, index) => (
                        <Cell
                            key={entry.label}
                            fill={entry.value >= 70 ? CHART_COLORS[2] : entry.value >= 40 ? CHART_COLORS[1] : CHART_COLORS[5]}
                            opacity={0.9 - index * 0.01}
                        />
                    ))}
                </Bar>
            </BarChart>
        </ResponsiveContainer>
    );
}

export function SkillPieChart({
    data,
    height = 240,
}: {
    data: { name: string; value: number }[];
    height?: number;
}) {
    return (
        <ResponsiveContainer width="100%" height={height}>
            <PieChart>
                <Pie
                    data={data}
                    dataKey="value"
                    nameKey="name"
                    innerRadius="55%"
                    outerRadius="85%"
                    paddingAngle={2}
                    stroke="none"
                >
                    {data.map((entry, index) => (
                        <Cell key={entry.name} fill={CHART_COLORS[index % CHART_COLORS.length]} />
                    ))}
                </Pie>
                {ChartTooltip({ suffix: '%' })}
                <Legend
                    verticalAlign="bottom"
                    height={36}
                    iconType="circle"
                    iconSize={8}
                    formatter={(value: string) => (
                        <span style={{ color: 'var(--surface-muted)', fontSize: 12 }}>{value}</span>
                    )}
                />
            </PieChart>
        </ResponsiveContainer>
    );
}
