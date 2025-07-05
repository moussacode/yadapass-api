@if($showDetails)
    <div class="space-y-4 p-4 bg-gray-50 rounded-lg border">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">{{ $fee->nom }}</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Montant total :</span>
                        <span class="font-medium">{{ number_format($fee->montant_total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Déjà payé :</span>
                        <span class="font-medium text-blue-600">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reste à payer :</span>
                        <span class="font-medium text-red-600">{{ number_format($reste, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col justify-center">
                <div class="mb-2">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        
                    </div>
                    <span class="text-xs text-gray-500 mt-1">{{ round($pourcentage, 1) }}% payé</span>
                </div>
                
                <div class="text-center">
                    @if($statusColor === 'success')
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            {{ $status }}
                        </span>
                    @elseif($statusColor === 'warning')
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                            {{ $status }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                            {{ $status }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <div class="text-sm text-gray-500 p-3 bg-gray-50 rounded-lg">
        {{ $message }}
    </div>
@endif