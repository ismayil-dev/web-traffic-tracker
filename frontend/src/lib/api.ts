const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface TopPage {
  url: string;
  title: string;
  visits: number;
  unique_visitors: number;
}

interface VisitorBreakdownItem {
  browser?: string;
  os?: string;
  device?: string;
  label: string;
  count: number;
  percentage: number;
}

interface VisitorBreakdownResponse {
  browsers: VisitorBreakdownItem[];
  operating_systems: VisitorBreakdownItem[];
  devices: VisitorBreakdownItem[];
}

interface RecentVisit {
  id: number;
  domain_id: number;
  url: string;
  page_title: string | null;
  visitor_ip: string;
  user_agent: string;
  browser: string;
  os: string;
  device: string;
  visitor_hash: string;
  timestamp: string;
  referrer: string | null;
}

interface HistoricalDataItem {
  date: string;
  unique_visitors: number;
  unique_pages: number;
  total_visits: number;
}

interface AnalyticsStats {
  period: string;
  stats: {
    date: string;
    unique_visitors: number;
    unique_pages: number;
    total_visits: number;
  };
}

interface OverallStats {
  total_visits: number;
  unique_visitors: number;
  tracking_since: string;
  last_activity: string;
}

interface ApiError {
  message: string;
  errors?: string[];
}

class ApiClient {
  private readonly baseUrl: string;
  private readonly token: string | null = null;

  constructor(baseUrl: string) {
    this.baseUrl = baseUrl;
    this.token = import.meta.env.VITE_API_TOKEN;
  }

  private async makeRequest<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const url = `${this.baseUrl}${endpoint}`;
    
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'X-Domain-Id': import.meta.env.VITE_DOMAIN_ID,
      ...options.headers as Record<string, string>,
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    const response = await fetch(url, {
      ...options,
      headers,
    });

    if (!response.ok) {
      const errorData: ApiError = await response.json().catch(() => ({
        message: `HTTP ${response.status}: ${response.statusText}`
      }));
      throw new Error(errorData.message || 'API request failed');
    }

    return response.json();
  }

  async getTopPages(params?: {
    period?: 'daily' | 'weekly' | 'monthly' | 'custom';
    from?: string;
    to?: string;
  }): Promise<TopPage[]> {
    const searchParams = new URLSearchParams();
    
    if (params?.period) searchParams.append('period', params.period);
    if (params?.from) searchParams.append('from', params.from);
    if (params?.to) searchParams.append('to', params.to);

    const query = searchParams.toString();
    const endpoint = `/analytics/top-pages${query ? `?${query}` : ''}`;
    
    return await this.makeRequest<TopPage>(endpoint);
  }

  async getVisitorBreakdown(params?: {
    period?: 'daily' | 'weekly' | 'monthly' | 'custom';
    from?: string;
    to?: string;
  }): Promise<VisitorBreakdownResponse> {
    const searchParams = new URLSearchParams();
    
    if (params?.period) searchParams.append('period', params.period);
    if (params?.from) searchParams.append('from', params.from);
    if (params?.to) searchParams.append('to', params.to);

    const query = searchParams.toString();
    const endpoint = `/analytics/visitor-breakdown`;
    
    return this.makeRequest<VisitorBreakdownResponse>(endpoint);
  }

  async getRecentVisits(params?: {
    limit?: number;
  }): Promise<RecentVisit[]> {
    const searchParams = new URLSearchParams();
    
    if (params?.limit) searchParams.append('limit', params.limit.toString());

    const query = searchParams.toString();
    const endpoint = `/analytics/recent-visits${query ? `?${query}` : ''}`;

    return await this.makeRequest<RecentVisit>(endpoint);
  }

  async getHistoricalData(params: {
    period: 'daily' | 'weekly' | 'monthly' | 'custom';
    from?: string;
    to?: string;
  }): Promise<HistoricalDataItem[]> {
    const searchParams = new URLSearchParams();
    
    searchParams.append('period', params.period);
    if (params.from) searchParams.append('from', params.from);
    if (params.to) searchParams.append('to', params.to);

    const query = searchParams.toString();
    const endpoint = `/analytics/historical?${query}`;
    
    return await this.makeRequest<HistoricalDataItem>(endpoint);
  }

  async getAnalyticsStats(params: {
    period: 'daily' | 'weekly' | 'monthly' | 'custom';
    from?: string;
    to?: string;
  }): Promise<AnalyticsStats> {
    const searchParams = new URLSearchParams();
    
    searchParams.append('period', params.period);
    if (params.from) searchParams.append('from', params.from);
    if (params.to) searchParams.append('to', params.to);

    const query = searchParams.toString();
    const endpoint = `/analytics?${query}`;
    
    return await this.makeRequest<AnalyticsStats>(endpoint);
  }

  async getOverallStats(): Promise<OverallStats> {
    return await this.makeRequest<OverallStats>('/analytics/summary');
  }
}

export const apiClient = new ApiClient(API_BASE_URL);
export type { TopPage, VisitorBreakdownItem, VisitorBreakdownResponse, RecentVisit, HistoricalDataItem, AnalyticsStats, OverallStats };