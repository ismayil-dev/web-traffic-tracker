import { useState, useEffect } from 'react';
import { apiClient, type RecentVisit } from '@/lib/api';
import { ExternalLink, MapPin, Clock } from 'lucide-react';
import {Loading} from "@/components/Loading.tsx";

export function RecentVisits() {
  const [recentVisits, setRecentVisits] = useState<RecentVisit[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchRecentVisits = async () => {
      setLoading(true);
      
      try {
        const visits = await apiClient.getRecentVisits({ limit: 20 });
        setRecentVisits(visits);
      } catch (err) {
        console.error('Failed to fetch recent visits:', err);
        setRecentVisits([]);
      } finally {
        setLoading(false);
      }
    };

    fetchRecentVisits();
  }, []);
  const formatTimeAgo = (timestamp: string) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return `${Math.floor(diffInMinutes / 1440)}d ago`;
  };

  const getPagePath = (url: string) => {
    try {
      return new URL(url).pathname;
    } catch {
      return url;
    }
  };

  const getBrowserColor = (browser: string) => {
    switch (browser.toLowerCase()) {
      case 'chrome': return 'bg-yellow-100 text-yellow-800';
      case 'firefox': return 'bg-orange-100 text-orange-800';
      case 'safari': return 'bg-blue-100 text-blue-800';
      case 'microsoft_edge': return 'bg-green-100 text-green-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getDeviceColor = (device: string) => {
    switch (device.toLowerCase()) {
      case 'desktop': return 'bg-purple-100 text-purple-800';
      case 'mobile': return 'bg-pink-100 text-pink-800';
      case 'tablet': return 'bg-indigo-100 text-indigo-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
        <span>Recent Activity</span>
        <div className="flex items-center space-x-1">
          <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span>Live</span>
        </div>
      </div>
      
      {loading && <Loading />}

      {!loading && recentVisits.length === 0 && (
        <div className="text-center py-8 text-gray-500">
          <Clock className="h-8 w-8 mx-auto mb-2 text-gray-300" />
          <p className="text-sm">No recent visits found</p>
        </div>
      )}
      
      {!loading && recentVisits.length > 0 && (
        <div className="space-y-4 max-h-96 overflow-y-auto">
          {recentVisits.map((visit) => (
          <div key={visit.id} className="border-l-2 border-gray-200 pl-4 pb-4">
            <div className="flex items-start justify-between">
              <div className="min-w-0 flex-1">
                <div className="flex items-center space-x-2 mb-1">
                  <span className="text-sm font-medium text-gray-900">
                    {visit.page_title || 'Untitled Page'}
                  </span>
                  <ExternalLink className="h-3 w-3 text-gray-400" />
                </div>
                
                <div className="text-xs text-gray-500 mb-2">
                  {getPagePath(visit.url)}
                </div>
                
                <div className="flex items-center space-x-2 mb-2">
                  <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getBrowserColor(visit.browser)}`}>
                    {visit.browser}
                  </span>
                  <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getDeviceColor(visit.device)}`}>
                    {visit.device}
                  </span>
                </div>
                
                <div className="flex items-center space-x-3 text-xs text-gray-500">
                  <div className="flex items-center space-x-1">
                    <Clock className="h-3 w-3" />
                    <span>{formatTimeAgo(visit.timestamp)}</span>
                  </div>
                  <div className="flex items-center space-x-1">
                    <MapPin className="h-3 w-3" />
                    <span>{visit.os}</span>
                  </div>
                </div>
                
                {visit.referrer && (
                  <div className="text-xs text-gray-400 mt-1">
                    From: {visit.referrer}
                  </div>
                )}
              </div>
              
              <div className="text-xs text-gray-400 ml-4">
                {visit.visitor_ip}
              </div>
            </div>
          </div>
          ))}
        </div>
      )}
      
      <div className="pt-4 border-t">
        <button className="text-sm text-blue-600 hover:text-blue-800 font-medium">
          View all visits →
        </button>
      </div>
    </div>
  );
}