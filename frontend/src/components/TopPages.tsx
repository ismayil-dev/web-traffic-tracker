import { useState, useEffect } from 'react';
import {apiClient, type TopPage} from '@/lib/api';
import { ExternalLink, TrendingUp } from 'lucide-react';
import {Loading} from "@/components/Loading.tsx";

export function TopPages() {
  const [topPages, setTopPages] = useState<TopPage[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchTopPages = async () => {
      setLoading(true);

      try {
        const pages = await apiClient.getTopPages({ period: 'daily'});
        setTopPages(pages);
      } catch (err) {
          console.error('Failed to fetch top pages:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchTopPages();
  }, []);

  const maxVisits = Math.max(...topPages.map(page => page.visits));

  const getBarWidth = (visits: number) => {
    return (visits / maxVisits) * 100;
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
        <span>Page</span>
        <span>Visits</span>
      </div>
      
      {loading && <Loading />}

      <div className="space-y-3">
        {topPages.map((page) => (
          <div key={page.url} className="space-y-2">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-2 min-w-0 flex-1">
                <div className="min-w-0 flex-1">
                  <div className="text-sm font-medium text-gray-900 truncate">
                    {page.title}
                  </div>
                  <div className="flex items-center space-x-1 text-xs text-gray-500">
                    <span>{page.url}</span>
                    <ExternalLink className="h-3 w-3" />
                  </div>
                </div>
              </div>
              <div className="flex items-center space-x-2">
                <div className="text-sm font-medium text-gray-900">
                  {page.visits.toLocaleString()}
                </div>
                <TrendingUp className="h-4 w-4 text-green-500" />
              </div>
            </div>
            
            {/* Progress bar */}
            <div className="w-full bg-gray-200 rounded-full h-1.5">
              <div 
                className="bg-blue-500 h-1.5 rounded-full transition-all duration-300"
                style={{ width: `${getBarWidth(page.visits)}%` }}
              />
            </div>
            
            {/* Unique visitors */}
            <div className="text-xs text-gray-500">
              {page.unique_visitors.toLocaleString()} unique visitors
            </div>
          </div>
        ))}
      </div>
      
      <div className="pt-4 border-t">
        <button className="text-sm text-blue-600 hover:text-blue-800 font-medium">
          View all pages →
        </button>
      </div>
    </div>
  );
}