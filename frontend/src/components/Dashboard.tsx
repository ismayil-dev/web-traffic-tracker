import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { MetricsCards } from './MetricsCards';
import { TrafficChart } from './TrafficChart';
import { TopPages } from './TopPages';
import { VisitorBreakdown } from './VisitorBreakdown';
import { RecentVisits } from './RecentVisits';
import { OverallStats } from './OverallStats';
import { BarChart3, Users, Globe, Activity, Calendar } from 'lucide-react';
import {Period} from "@/lib/period.ts";

export function Dashboard() {
  const [selectedPeriod, setSelectedPeriod] = useState<Period>(Period.DAILY);
  const [customDateFrom, setCustomDateFrom] = useState('');
  const [customDateTo, setCustomDateTo] = useState('');
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [appliedCustomDates, setAppliedCustomDates] = useState<{from: string, to: string} | null>(null);

  const handlePeriodChange = (period: Period) => {
    setSelectedPeriod(period);
    setShowDatePicker(period === Period.CUSTOM);
    if (period !== Period.CUSTOM) {
      setAppliedCustomDates(null);
    }
  };

  const handleApplyCustomDates = () => {
    if (customDateFrom && customDateTo) {
      setAppliedCustomDates({ from: customDateFrom, to: customDateTo });
      setShowDatePicker(false);
    }
  };

  const periodButtons = [
    { value: Period.DAILY, label: 'Daily' },
    { value: Period.WEEKLY, label: 'Weekly' },
    { value: Period.MONTHLY, label: 'Monthly' },
    { value: Period.CUSTOM, label: 'Custom' },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-6">
            <div className="flex items-center space-x-3">
              <div className="p-2 bg-blue-600 rounded-lg">
                <BarChart3 className="h-6 w-6 text-white" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-gray-900">Traffic Analytics</h1>
                <p className="text-sm text-gray-600">Monitor your website traffic and visitor behavior</p>
              </div>
            </div>
            
            <div className="flex items-center space-x-2">
              <span className="text-sm font-medium text-gray-700">Period:</span>
              <div className="flex space-x-1">
                {periodButtons.map((period) => (
                  <Button
                    key={period.value}
                    variant={selectedPeriod === period.value ? "default" : "outline"}
                    size="sm"
                    onClick={() => handlePeriodChange(period.value)}
                    className="text-xs"
                  >
                    {period.label}
                  </Button>
                ))}
              </div>
            </div>
          </div>
        </div>
      </header>

      {showDatePicker && (
        <div className="bg-white border-b shadow-sm">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex items-center justify-center py-4 space-x-4">
              <div className="flex items-center space-x-2">
                <Calendar className="h-4 w-4 text-gray-500" />
                <span className="text-sm font-medium text-gray-700">From:</span>
                <input
                  type="date"
                  value={customDateFrom}
                  onChange={(e) => setCustomDateFrom(e.target.value)}
                  className="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div className="flex items-center space-x-2">
                <span className="text-sm font-medium text-gray-700">To:</span>
                <input
                  type="date"
                  value={customDateTo}
                  onChange={(e) => setCustomDateTo(e.target.value)}
                  className="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <Button
                onClick={handleApplyCustomDates}
                size="sm"
                className="ml-4"
              >
                Apply
              </Button>
            </div>
          </div>
        </div>
      )}

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Overall Stats - All Time */}
        <div className="mb-8">
          <OverallStats />
        </div>

        {/* Metrics Cards - Period Based */}
        <div className="mb-8">
          <MetricsCards 
            period={selectedPeriod} 
            customDates={selectedPeriod === Period.CUSTOM ? appliedCustomDates : null}
          />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
          {/* Traffic Overview Chart */}
          <div className="lg:col-span-2 xl:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Activity className="h-5 w-5 text-blue-600" />
                  <span>Traffic Overview</span>
                </CardTitle>
              </CardHeader>
              <CardContent className={"overflow-auto pb-6"}>
                <TrafficChart 
                  period={selectedPeriod} 
                  customDates={selectedPeriod === Period.CUSTOM ? appliedCustomDates : null}
                />
              </CardContent>
            </Card>
          </div>

          <div className="xl:col-span-1">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Globe className="h-5 w-5 text-green-600" />
                  <span>Top Pages</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <TopPages />
              </CardContent>
            </Card>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <div>
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Users className="h-5 w-5 text-purple-600" />
                  <span>Visitor Breakdown</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <VisitorBreakdown />
              </CardContent>
            </Card>
          </div>

          <div>
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Activity className="h-5 w-5 text-orange-600" />
                  <span>Recent Visits</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <RecentVisits />
              </CardContent>
            </Card>
          </div>
        </div>

      </main>
    </div>
  );
}