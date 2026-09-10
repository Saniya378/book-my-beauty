@if (session('success'))
    <div style="background: #e8f5e9; color: #2e7d32; border-left: 4px solid #2e7d32; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div><strong>Success:</strong> {!! session('success') !!}</div>
        <span onclick="this.parentElement.remove()" style="cursor: pointer; font-size: 18px; font-weight: bold;">&times;</span>
    </div>
@endif

@if (session('error'))
    <div style="background: #ffebee; color: #c62828; border-left: 4px solid #c62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div><strong>Error:</strong> {{ session('error') }}</div>
        <span onclick="this.parentElement.remove()" style="cursor: pointer; font-size: 18px; font-weight: bold;">&times;</span>
    </div>
@endif

@if (session('message'))
    <div style="background: #e3f2fd; color: #1565c0; border-left: 4px solid #1565c0; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div>{{ session('message') }}</div>
        <span onclick="this.parentElement.remove()" style="cursor: pointer; font-size: 18px; font-weight: bold;">&times;</span>
    </div>
@endif

@if ($errors->any())
    <div style="background: #ffebee; color: #c62828; border-left: 4px solid #c62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <strong>Please check the following errors:</strong>
        <ul style="margin: 8px 0 0 18px;">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif
