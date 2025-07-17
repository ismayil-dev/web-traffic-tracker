import { useState, useEffect } from 'react';
import { apiClient, type HistoricalDataItem } from '@/lib/api';
import {Period} from "@/lib/period.ts";
import {Loading} from "@/components/Loading.tsx";

interface TrafficChartProps {
  period: Period;
  customDates?: {from: string, to: string} | null;
}

export function TrafficChart({ period, customDates }: TrafficChartProps) {
  const [data, setData] = useState<HistoricalDataItem[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Don't fetch data for custom period until dates are selected
    if (period === Period.CUSTOM && !customDates) {
      setData([]);
      setLoading(false);
      return;
    }

    const fetchHistoricalData = async () => {
      setLoading(true);
      
      try {
        const historicalData = await apiClient.getHistoricalData({ 
          period: period.toLowerCase() as 'daily' | 'weekly' | 'monthly' | 'custom',
          from: customDates?.from,
          to: customDates?.to
        });
        setData(historicalData);
      } catch (err) {
        console.error('Failed to fetch historical data:', err);
        setData([]);
      } finally {
        setLoading(false);
      }
    };

    fetchHistoricalData();
  }, [period, customDates]);
  
  const maxVisits = data.length > 0 ? Math.max(...data.map(d => d.total_visits)) : 0;
  const maxVisitors = data.length > 0 ? Math.max(...data.map(d => d.unique_visitors)) : 0;
  const maxValue = Math.max(maxVisits, maxVisitors, 1); // Ensure at least 1 to avoid division by zero

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric' 
    });
  };

  const getBarHeight = (value: number) => {
    return (value / maxValue) * 200; // 200px max height
  };

  if (loading) {
    return (
      <div className="h-64 pb-5 flex items-center justify-center">
        <Loading />
      </div>
    );
  }

  if (period === Period.CUSTOM && !customDates) {
    return (
      <div className="h-64 pb-5 flex items-center justify-center">
        <div className="text-center text-gray-500">
          <div className="text-sm">Please select a date range to view chart data</div>
        </div>
      </div>
    );
  }

  if (data.length === 0) {
    return (
      <div className="h-64 pb-5 flex items-center justify-center">
        <div className="text-center text-gray-500">
          <div className="text-sm">No data available for this period</div>
        </div>
      </div>
    );
  }

  return (
    <div className="h-64 pb-5">
      <div className="flex items-end justify-between h-full space-x-2">
        {data.map((item, index) => (
          <div key={index} className="flex-1 flex flex-col items-center">
            <div className="flex items-end space-x-1 mb-2">
              <div className="relative group">
                <div
                  className="w-4 bg-blue-500 rounded-t-sm hover:bg-blue-600 transition-colors"
                  style={{ height: `${getBarHeight(item.total_visits)}px` }}
                />
                <div className="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                  Total Visits: {item.total_visits.toLocaleString()}
                </div>
              </div>
              
              <div className="relative group">
                <div
                  className="w-4 bg-green-500 rounded-t-sm hover:bg-green-600 transition-colors"
                  style={{ height: `${getBarHeight(item.unique_visitors)}px` }}
                />
                <div className="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                  Unique Visitors: {item.unique_visitors.toLocaleString()}
                </div>
              </div>
            </div>
            
            <div className="text-xs text-gray-500 mt-1 text-center">
              {formatDate(item.date)}
            </div>
          </div>
        ))}
      </div>
      
      <div className="flex justify-center space-x-6 mt-2">
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 bg-blue-500 rounded-sm"></div>
          <span className="text-sm text-gray-600">Total Visits</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 bg-green-500 rounded-sm"></div>
          <span className="text-sm text-gray-600">Unique Visitors</span>
        </div>
      </div>
    </div>
  );
}