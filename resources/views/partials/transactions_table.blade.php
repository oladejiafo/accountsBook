<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer ID</th>
            <th>Transaction Details</th>
            <th>Date</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->customer_id }}</td>
                <td>{{ $transaction->details }}</td>
                <td>{{ $transaction->created_at }}</td>
                <td>{{ $transaction->amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
