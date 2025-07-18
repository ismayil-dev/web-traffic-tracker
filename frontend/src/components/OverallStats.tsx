import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { apiClient, type OverallStats as OverallStatsType } from '@/lib/api';
import { Users, MousePointer, Calendar, Clock, TrendingUp } from 'lucide-react';
import { Loading } from './Loading';

export function OverallStats() {
  const [stats, setStats] = useState<OverallStatsType | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        setLoading(true);
        setError(null);
        const data = await apiClient.getOverallStats();
        setStats(data);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load overall statistics');
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return (
      <Card className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
        <CardContent className="p-6">
          <Loading />
        </CardContent>
      </Card>
    );
  }

  if (error) {
    return (
      <Card className="bg-gradient-to-r from-red-50 to-pink-50 border-red-200">
        <CardContent className="p-6">
          <div className="text-center text-red-600">
            <p className="font-medium">Failed to load overall statistics</p>
            <p className="text-sm mt-1">{error}</p>
          </div>
        </CardContent>
      </Card>
    );
  }

  if (!stats) return null;

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const daysSinceTracking = Math.floor(
    (new Date().getTime() - new Date(stats.tracking_since).getTime()) / (1000 * 60 * 60 * 24)
  );

  const avgVisitsPerDay = daysSinceTracking > 0 ? Math.round(stats.total_visits / daysSinceTracking) : stats.total_visits;
  const avgVisitsPerVisitor = stats.unique_visitors > 0 ? Math.round((stats.total_visits / stats.unique_visitors) * 10) / 10 : 0;

  const getLastAcitivityDaysAgo = (): string => {
    const days = Math.floor((new Date().getTime() - new Date(stats.last_activity).getTime()) / (1000 * 60 * 60 * 24));
    return days > 0 ? `${days} days ago` : 'today';
  };

  return (
    <Card className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200 shadow-lg">
      <CardHeader className="pb-3">
        <CardTitle className="flex items-center space-x-2 text-blue-900">
          <TrendingUp className="h-6 w-6 text-blue-600" />
          <span>Overall Analytics</span>
          <span className="text-xs font-normal text-blue-600 ml-auto">All Time</span>
        </CardTitle>
      </CardHeader>
      <CardContent className="pt-0">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {/* Total Visits */}
          <div className="text-center p-4 bg-white/60 rounded-lg border border-blue-100">
            <div className="flex justify-center mb-2">
              <MousePointer className="h-8 w-8 text-blue-600" />
            </div>
            <div className="text-2xl font-bold text-blue-900">
              {stats.total_visits.toLocaleString()}
            </div>
            <div className="text-xs text-blue-600 font-medium">Total Visits</div>
            <div className="text-xs text-blue-500 mt-1">
              ~{avgVisitsPerDay}/day
            </div>
          </div>

          {/* Unique Visitors */}
          <div className="text-center p-4 bg-white/60 rounded-lg border border-blue-100">
            <div className="flex justify-center mb-2">
              <Users className="h-8 w-8 text-green-600" />
            </div>
            <div className="text-2xl font-bold text-green-900">
              {stats.unique_visitors.toLocaleString()}
            </div>
            <div className="text-xs text-green-600 font-medium">Unique Visitors</div>
            <div className="text-xs text-green-500 mt-1">
              {avgVisitsPerVisitor} visits/visitor
            </div>
          </div>

          {/* Tracking Since */}
          <div className="text-center p-4 bg-white/60 rounded-lg border border-blue-100">
            <div className="flex justify-center mb-2">
              <Calendar className="h-8 w-8 text-purple-600" />
            </div>
            <div className="text-sm font-bold text-purple-900">
              {formatDate(stats.tracking_since)}
            </div>
            <div className="text-xs text-purple-600 font-medium">Tracking Since</div>
            <div className="text-xs text-purple-500 mt-1">
              {daysSinceTracking} days ago
            </div>
          </div>

          {/* Last Activity */}
          <div className="text-center p-4 bg-white/60 rounded-lg border border-blue-100">
            <div className="flex justify-center mb-2">
              <Clock className="h-8 w-8 text-orange-600" />
            </div>
            <div className="text-sm font-bold text-orange-900">
              {formatDate(stats.last_activity)}
            </div>
            <div className="text-xs text-orange-600 font-medium">Last Activity</div>
            <div className="text-xs text-orange-500 mt-1">
              {getLastAcitivityDaysAgo()}
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}