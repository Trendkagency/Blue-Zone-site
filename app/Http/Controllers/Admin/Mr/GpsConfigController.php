<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\GpsConfig;
use App\Models\User;
use Illuminate\Http\Request;

class GpsConfigController extends Controller
{
    public function index()
    {
        $globalConfig = GpsConfig::whereNull('user_id')->first();
        if (!$globalConfig) {
            $globalConfig = GpsConfig::create([
                'user_id' => null,
                'allowed_radius_m' => 150,
                'is_gps_required' => true,
                'mock_detection_enabled' => true,
            ]);
        }

        $repConfigs = GpsConfig::with('user')->whereNotNull('user_id')->get();
        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        return view('admin.mr.gps-config.index', compact('globalConfig', 'repConfigs', 'medicalReps'));
    }

    public function updateGlobal(Request $request)
    {
        $validated = $request->validate([
            'allowed_radius_m' => 'required|integer|min:20|max:5000',
            'is_gps_required' => 'nullable|boolean',
            'mock_detection_enabled' => 'nullable|boolean',
        ]);

        $validated['is_gps_required'] = $request->has('is_gps_required');
        $validated['mock_detection_enabled'] = $request->has('mock_detection_enabled');

        $globalConfig = GpsConfig::whereNull('user_id')->first();
        if ($globalConfig) {
            $globalConfig->update($validated);
        } else {
            $validated['user_id'] = null;
            GpsConfig::create($validated);
        }

        return redirect()->route('admin.mr.gps-config.index')
            ->with('success', __('admin.mr.gps_global_config_updated_successfully'));
    }

    public function storeRepConfig(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'allowed_radius_m' => 'required|integer|min:20|max:5000',
            'is_gps_required' => 'nullable|boolean',
            'mock_detection_enabled' => 'nullable|boolean',
        ]);

        $validated['is_gps_required'] = $request->has('is_gps_required');
        $validated['mock_detection_enabled'] = $request->has('mock_detection_enabled');

        GpsConfig::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        return redirect()->route('admin.mr.gps-config.index')
            ->with('success', __('admin.mr.gps_rep_config_saved_successfully'));
    }

    public function destroyRepConfig(int $id)
    {
        $config = GpsConfig::whereNotNull('user_id')->findOrFail($id);
        $config->delete();

        return redirect()->route('admin.mr.gps-config.index')
            ->with('success', __('admin.mr.gps_rep_override_removed_successfully'));
    }
}
