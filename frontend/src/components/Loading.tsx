import {Loader2} from "lucide-react";


export function Loading() {
    return (
        <div className="flex items-center justify-center py-8">
            <Loader2 className="h-6 w-6 animate-spin text-blue-500" />
            <span className="ml-2 text-sm text-gray-500">Loading...</span>
        </div>
    );
}