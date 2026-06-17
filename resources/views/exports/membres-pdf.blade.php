<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
        }

        /* En-tête */
        .header {
            padding: 16px 20px 12px;
            border-bottom: 3px solid #1d4ed8;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header-title { font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .header-sub { font-size: 9px; color: #64748b; margin-top: 3px; }
        .header-meta { text-align: right; font-size: 8px; color: #64748b; }

        /* Badges statut */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-active    { background: #dcfce7; color: #166534; }
        .badge-candidate { background: #fef9c3; color: #854d0e; }
        .badge-suspended { background: #fee2e2; color: #991b1b; }
        .badge-excluded  { background: #f1f5f9; color: #475569; }
        .badge-alumni    { background: #ede9fe; color: #5b21b6; }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        thead tr {
            background-color: #1d4ed8;
            color: #fff;
        }
        thead th {
            padding: 6px 5px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #1e3a8a;
        }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        tbody tr:hover           { background-color: #eff6ff; }
        tbody td {
            padding: 5px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        /* Pied de page */
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* Résumé stats */
        .stats-bar {
            display: flex;
            gap: 12px;
            margin: 10px 0 0;
            padding: 8px 12px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 8.5px;
        }
        .stat-item { text-align: center; }
        .stat-value { font-size: 13px; font-weight: bold; color: #1d4ed8; }
        .stat-label { color: #64748b; margin-top: 1px; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <div class="header-title">Réseau des Jeunes Entrepreneurs des Comores</div>
            <div class="header-sub">Liste des membres — Export officiel</div>
        </div>
        <div class="header-meta">
            Généré le {{ now()->format('d/m/Y à H:i') }}<br>
            Total : {{ $membres->count() }} membre(s)
        </div>
    </div>

    @php
        $statusLabels = [
            'active'    => ['label' => 'Actif',    'class' => 'badge-active'],
            'candidate' => ['label' => 'Candidat', 'class' => 'badge-candidate'],
            'suspended' => ['label' => 'Suspendu', 'class' => 'badge-suspended'],
            'excluded'  => ['label' => 'Exclu',    'class' => 'badge-excluded'],
            'alumni'    => ['label' => 'Alumni',   'class' => 'badge-alumni'],
        ];
        $nbActifs    = $membres->where('membership_status', 'active')->count();
        $nbCandidats = $membres->where('membership_status', 'candidate')->count();
        $nbSuspendus = $membres->where('membership_status', 'suspended')->count();
    @endphp

    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-value">{{ $membres->count() }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color:#166534;">{{ $nbActifs }}</div>
            <div class="stat-label">Actifs</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color:#854d0e;">{{ $nbCandidats }}</div>
            <div class="stat-label">Candidats</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color:#991b1b;">{{ $nbSuspendus }}</div>
            <div class="stat-label">Suspendus</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Entreprise / Projet</th>
                <th>Secteur</th>
                <th>Ville</th>
                <th>Statut</th>
                <th>Adhésion</th>
                <th>Expiration</th>
            </tr>
        </thead>
        <tbody>
            @forelse($membres as $i => $profile)
                @php
                    $st = $statusLabels[$profile->membership_status] ?? ['label' => $profile->membership_status, 'class' => ''];
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $profile->user->first_name }} {{ $profile->user->last_name }}</strong></td>
                    <td>{{ $profile->user->email }}</td>
                    <td>{{ $profile->user->phone ?? '—' }}</td>
                    <td>{{ $profile->company_name ?? $profile->project_name ?? '—' }}</td>
                    <td>{{ $profile->sector ?? '—' }}</td>
                    <td>{{ $profile->city ?? '—' }}</td>
                    <td><span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                    <td>{{ $profile->activated_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $profile->membership_expires_at?->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:16px;color:#94a3b8;">
                        Aucun membre trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Réseau des Jeunes Entrepreneurs des Comores — Document confidentiel</span>
        <span>contact@reseau-entrepreneurs.km</span>
    </div>

</body>
</html>
