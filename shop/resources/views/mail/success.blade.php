<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body > 
    <h2>Đơn hàng của bạn</h2>
    <table border="1" cellpadding="20" cellspacing="0">
        @php $total = 0; @endphp
        <tr>
            <td><b>Tên sản phẩm</b></td>
            <td><b>Đơn giá</b></td>
            <td><b>Số lượng</b></td>
            <td><b>Tổng</b></td>
        </tr>
        @foreach($products as $key => $product)
            @php
                $price = $product->price_sale != 0 ? $product->price_sale : $product->price;
                $priceEnd = $price * $carts[$product->id];
                $total += $priceEnd;
            @endphp
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($price, 0, '', '.') }}</td>
                <td>{{ $carts[$product->id] }}</td>
                <td>{{ number_format($priceEnd, 0, '', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3">
                <h4>Cart Totals</h4>
            </td>
            <td>{{ number_format($total, 0, '', '.') }}</td>
        </tr>
    </table>
</body>
</html>

