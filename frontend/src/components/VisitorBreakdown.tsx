import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import {apiClient, type VisitorBreakdownItem, type VisitorBreakdownResponse} from '@/lib/api';
import {Loading} from "@/components/Loading.tsx";

type BreakdownType = 'browser' | 'os' | 'device';

export function VisitorBreakdown() {
  const [activeTab, setActiveTab] = useState<BreakdownType>('browser');
  const [breakdownData, setBreakdownData] = useState<VisitorBreakdownResponse>({
    browsers: [],
    operating_systems: [],
    devices: [],
  });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchBreakdownData = async () => {
      setLoading(true);
      
      try {
        const data = await apiClient.getVisitorBreakdown({ period: 'daily' });
        setBreakdownData(data);
      } finally {
        setLoading(false);
      }
    };

    fetchBreakdownData();
  }, []);

  const renderBreakdown = () => {
    let data: VisitorBreakdownItem[];
    
    switch (activeTab) {
      case 'browser':
        data = breakdownData.browsers;
        break;
      case 'os':
        data = breakdownData.operating_systems;
        break;
      case 'device':
        data = breakdownData.devices;
        break;
    }

    return (
      <div className="space-y-3">
        {data.map((item, index) => (
          <div key={index} className="flex items-center justify-between">
            <div className="flex items-center space-x-3 min-w-0 flex-1">
              <div className="min-w-0 flex-1">
                <div className="text-sm font-medium text-gray-900">
                  {item.label}
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2 mt-1">
                  <div 
                    className="bg-blue-500 h-2 rounded-full transition-all duration-300"
                    style={{ width: `${item.percentage}%` }}
                  />
                </div>
              </div>
            </div>
            <div className="text-right ml-4">
              <div className="text-sm font-medium text-gray-900">
                {item.count.toLocaleString()}
              </div>
              <div className="text-xs text-gray-500">
                {item.percentage}%
              </div>
            </div>
          </div>
        ))}
      </div>
    );
  };

  return (
    <div className="space-y-6">
      {/* Tab Navigation */}
      <div className="flex space-x-1 bg-gray-100 p-1 rounded-lg">
        <Button
          variant={activeTab === 'browser' ? 'default' : 'ghost'}
          size="sm"
          onClick={() => setActiveTab('browser')}
          className="flex-1 text-xs"
        >
          Browser
        </Button>
        <Button
          variant={activeTab === 'os' ? 'default' : 'ghost'}
          size="sm"
          onClick={() => setActiveTab('os')}
          className="flex-1 text-xs"
        >
          OS
        </Button>
        <Button
          variant={activeTab === 'device' ? 'default' : 'ghost'}
          size="sm"
          onClick={() => setActiveTab('device')}
          className="flex-1 text-xs"
        >
          Device
        </Button>
      </div>

      {loading && <Loading />}

      {/* Breakdown Content */}
      {!loading && renderBreakdown()}
    </div>
  );
}