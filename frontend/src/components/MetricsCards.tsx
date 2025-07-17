import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { apiClient, type AnalyticsStats } from '@/lib/api';
import { Users, Eye, FileText, Loader2 } from 'lucide-react';
import {Period} from "@/lib/period.ts";

interface MetricsCardsProps {
  period: Period;
  customDates?: {from: string, to: string} | null;
}

export function MetricsCards({ period, customDates }: MetricsCardsProps) {
  const [stats, setStats] = useState<AnalyticsStats | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Don't fetch data for custom period until dates are selected
    if (period === Period.CUSTOM && !customDates) {
      setStats(null);
      setLoading(false);
      return;
    }

    const fetchAnalyticsStats = async () => {
      setLoading(true);
      
      try {
        const analyticsData = await apiClient.getAnalyticsStats({ 
          period: period.toLowerCase() as 'daily' | 'weekly' | 'monthly' | 'custom',
          from: customDates?.from,
          to: customDates?.to
        });
        setStats(analyticsData);
      } catch (err) {
        console.error('Failed to fetch analytics stats:', err);
        setStats(null);
      } finally {
        setLoading(false);
      }
    };

    fetchAnalyticsStats();
  }, [period, customDates]);

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {[1, 2, 3].map((i) => (
          <Card key={i} className="relative overflow-hidden">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <div className="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
              <div className="w-8 h-8 bg-gray-200 rounded-lg animate-pulse"></div>
            </CardHeader>
            <CardContent>
              <div className="flex items-center justify-center py-8">
                <Loader2 className="h-6 w-6 animate-spin text-blue-500" />
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  if (period === Period.CUSTOM && !customDates) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {[1, 2, 3].map((i) => (
          <Card key={i} className="relative overflow-hidden">
            <CardContent>
              <div className="flex items-center justify-center py-8 text-gray-500">
                <div className="text-center">
                  <div className="text-sm">Please select a date range</div>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  if (!stats) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {[1, 2, 3].map((i) => (
          <Card key={i} className="relative overflow-hidden">
            <CardContent>
              <div className="flex items-center justify-center py-8 text-gray-500">
                <div className="text-center">
                  <div className="text-sm">No data available</div>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  const metrics = [
    {
      title: 'Unique Visitors',
      value: stats.stats.unique_visitors.toLocaleString(),
      icon: Users,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
    },
    {
      title: 'Total Visits',
      value: stats.stats.total_visits.toLocaleString(),
      icon: Eye,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
    },
    {
      title: 'Unique Pages',
      value: stats.stats.unique_pages.toLocaleString(),
      icon: FileText,
      color: 'text-purple-600',
      bgColor: 'bg-purple-50',
    },
  ];

  const getPeriodLabel = (period: Period): string => {
    switch (period) {
      case Period.DAILY: return 'today';
      case Period.WEEKLY: return 'this week';
      case Period.MONTHLY: return 'this month';
      default: return 'selected period';
    }
  };

  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      {metrics.map((metric) => {
        const Icon = metric.icon;
        
        return (
          <Card key={metric.title} className="relative overflow-hidden">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium text-gray-600">
                {metric.title}
              </CardTitle>
              <div className={`p-2 rounded-lg ${metric.bgColor}`}>
                <Icon className={`h-4 w-4 ${metric.color}`} />
              </div>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold text-gray-900">
                {metric.value}
              </div>
              <p className="text-xs text-gray-500 mt-1">
                {getPeriodLabel(period)}
              </p>
            </CardContent>
          </Card>
        );
      })}
    </div>
  );
}