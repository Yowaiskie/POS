import { useState } from "react";
import {
  TrendingUp,
  TrendingDown,
  PhilippinePeso,
  CreditCard,
  Users,
  Package,
  BarChart3,
} from "lucide-react";

type TimePeriod = "daily" | "weekly" | "monthly";

export function ReportsPage() {
  const [period, setPeriod] = useState<TimePeriod>("daily");

  // Mock data - in a real app, this would come from your backend
  const salesData = {
    daily: { total: 1850, roomRental: 1200, shortOrders: 650 },
    weekly: {
      total: 12950,
      roomRental: 8400,
      shortOrders: 4550,
    },
    monthly: {
      total: 54300,
      roomRental: 35200,
      shortOrders: 19100,
    },
  };

  const paymentData = {
    cash: { amount: 28500, transactions: 142 },
    gcash: { amount: 25800, transactions: 98 },
  };

  const topSellingItems = [
    { name: "Cheesy Katsu", quantity: 245, revenue: 21805 },
    { name: "Hungarian", quantity: 189, revenue: 19845 },
    { name: "Hungarian + Tapa", quantity: 134, revenue: 20100 },
    {
      name: "Beef Pepper Mushroom",
      quantity: 167,
      revenue: 20875,
    },
    { name: "Tocilog", quantity: 198, revenue: 17622 },
  ];

  const leastSellingItems = [
    { name: "Cornedbeef", quantity: 12, revenue: 900 },
    { name: "Bacon + Bagnet", quantity: 18, revenue: 2340 },
    { name: "Chicken Skin", quantity: 23, revenue: 2047 },
  ];

  const staffPerformance = [
    {
      name: "John Doe",
      transactions: 87,
      totalSales: 18450,
      errors: 2,
    },
    {
      name: "Jane Smith",
      transactions: 92,
      totalSales: 21300,
      errors: 1,
    },
    {
      name: "Mike Johnson",
      transactions: 61,
      totalSales: 14550,
      errors: 3,
    },
  ];

  const currentSales = salesData[period];

  return (
    <div className="p-4 md:p-8 max-w-[1600px] mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">
          Reports & Analytics
        </h1>
        <p className="text-slate-600">
          Track performance, sales, and staff metrics
        </p>
      </div>

      {/* Period Selector */}
      <div className="flex gap-3 mb-8">
        {(["daily", "weekly", "monthly"] as TimePeriod[]).map(
          (p) => (
            <button
              key={p}
              onClick={() => setPeriod(p)}
              className={`px-6 py-3 rounded-lg font-semibold transition-all capitalize ${
                period === p
                  ? "bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg"
                  : "bg-white border-2 border-slate-200 text-slate-700 hover:border-indigo-400"
              }`}
            >
              {p}
            </button>
          ),
        )}
      </div>

      {/* Sales Overview */}
      <div className="mb-10">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-1 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
          <h2 className="text-xl md:text-2xl font-bold text-slate-900">
            Sales Overview
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            className="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm font-medium text-slate-600">
                Total Sales
              </span>
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                <PhilippinePeso className="w-6 h-6 text-white" />
              </div>
            </div>
            <div className="text-4xl font-bold text-emerald-600">
              ₱{currentSales.total.toLocaleString()}
            </div>
            <div className="text-xs text-slate-500 mt-2 capitalize">
              {period} total
            </div>
          </div>

          <div
            className="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm font-medium text-slate-600">
                Room Rental
              </span>
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                <Package className="w-6 h-6 text-white" />
              </div>
            </div>
            <div className="text-4xl font-bold text-blue-600">
              ₱{currentSales.roomRental.toLocaleString()}
            </div>
            <div className="text-xs text-slate-500 mt-2">
              {Math.round(
                (currentSales.roomRental / currentSales.total) *
                  100,
              )}
              % of total
            </div>
          </div>

          <div
            className="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm font-medium text-slate-600">
                Short Orders
              </span>
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                <BarChart3 className="w-6 h-6 text-white" />
              </div>
            </div>
            <div className="text-4xl font-bold text-purple-600">
              ₱{currentSales.shortOrders.toLocaleString()}
            </div>
            <div className="text-xs text-slate-500 mt-2">
              {Math.round(
                (currentSales.shortOrders /
                  currentSales.total) *
                  100,
              )}
              % of total
            </div>
          </div>
        </div>
      </div>

      {/* Payment Methods */}
      <div className="mb-10">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-1 h-6 bg-gradient-to-b from-blue-500 to-cyan-500 rounded-full"></div>
          <h2 className="text-xl md:text-2xl font-bold text-slate-900">
            Payment Methods
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            className="bg-white border border-slate-200 rounded-xl p-6"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <div className="flex items-center gap-4 mb-4">
              <div className="w-14 h-14 rounded-xl bg-emerald-100 flex items-center justify-center">
                <PhilippinePeso className="w-7 h-7 text-emerald-600" />
              </div>
              <div className="flex-1">
                <h3 className="font-bold text-slate-900 text-lg">
                  Cash
                </h3>
                <p className="text-sm text-slate-500">
                  {paymentData.cash.transactions} transactions
                </p>
              </div>
              <div className="text-right">
                <div className="text-2xl font-bold text-emerald-600">
                  ₱{paymentData.cash.amount.toLocaleString()}
                </div>
              </div>
            </div>
            <div className="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div
                className="h-full bg-gradient-to-r from-emerald-500 to-teal-500"
                style={{
                  width: `${(paymentData.cash.amount / (paymentData.cash.amount + paymentData.gcash.amount)) * 100}%`,
                }}
              ></div>
            </div>
          </div>

          <div
            className="bg-white border border-slate-200 rounded-xl p-6"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <div className="flex items-center gap-4 mb-4">
              <div className="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                <CreditCard className="w-7 h-7 text-blue-600" />
              </div>
              <div className="flex-1">
                <h3 className="font-bold text-slate-900 text-lg">
                  G-Cash
                </h3>
                <p className="text-sm text-slate-500">
                  {paymentData.gcash.transactions} transactions
                </p>
              </div>
              <div className="text-right">
                <div className="text-2xl font-bold text-blue-600">
                  ₱{paymentData.gcash.amount.toLocaleString()}
                </div>
              </div>
            </div>
            <div className="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div
                className="h-full bg-gradient-to-r from-blue-500 to-cyan-500"
                style={{
                  width: `${(paymentData.gcash.amount / (paymentData.cash.amount + paymentData.gcash.amount)) * 100}%`,
                }}
              ></div>
            </div>
          </div>
        </div>
      </div>

      {/* Product Performance */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        {/* Top Selling */}
        <div>
          <div className="flex items-center gap-3 mb-5">
            <div className="w-1 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
            <h2 className="text-xl md:text-2xl font-bold text-slate-900">
              Top Selling Items
            </h2>
          </div>
          <div
            className="bg-white border border-slate-200 rounded-xl overflow-hidden"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <table className="w-full">
              <thead className="bg-gradient-to-r from-emerald-50 to-teal-50 border-b-2 border-slate-200">
                <tr>
                  <th className="text-left px-6 py-4 text-sm font-semibold text-slate-700">
                    Item
                  </th>
                  <th className="text-center px-6 py-4 text-sm font-semibold text-slate-700">
                    Qty
                  </th>
                  <th className="text-right px-6 py-4 text-sm font-semibold text-slate-700">
                    Revenue
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200">
                {topSellingItems.map((item, index) => (
                  <tr
                    key={index}
                    className="hover:bg-slate-50 transition-colors"
                  >
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
                          {index + 1}
                        </div>
                        <span className="font-medium text-slate-900">
                          {item.name}
                        </span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-center text-slate-600 font-semibold">
                      {item.quantity}
                    </td>
                    <td className="px-6 py-4 text-right text-emerald-600 font-bold">
                      ₱{item.revenue.toLocaleString()}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Least Selling */}
        <div>
          <div className="flex items-center gap-3 mb-5">
            <div className="w-1 h-6 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full"></div>
            <h2 className="text-xl md:text-2xl font-bold text-slate-900">
              Least Selling Items
            </h2>
          </div>
          <div
            className="bg-white border border-slate-200 rounded-xl overflow-hidden"
            style={{ boxShadow: "var(--shadow-md)" }}
          >
            <table className="w-full">
              <thead className="bg-gradient-to-r from-amber-50 to-orange-50 border-b-2 border-slate-200">
                <tr>
                  <th className="text-left px-6 py-4 text-sm font-semibold text-slate-700">
                    Item
                  </th>
                  <th className="text-center px-6 py-4 text-sm font-semibold text-slate-700">
                    Qty
                  </th>
                  <th className="text-right px-6 py-4 text-sm font-semibold text-slate-700">
                    Revenue
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200">
                {leastSellingItems.map((item, index) => (
                  <tr
                    key={index}
                    className="hover:bg-slate-50 transition-colors"
                  >
                    <td className="px-6 py-4 font-medium text-slate-900">
                      {item.name}
                    </td>
                    <td className="px-6 py-4 text-center text-slate-600 font-semibold">
                      {item.quantity}
                    </td>
                    <td className="px-6 py-4 text-right text-amber-600 font-bold">
                      ₱{item.revenue.toLocaleString()}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Staff Performance */}
      <div className="mb-10">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-1 h-6 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></div>
          <h2 className="text-xl md:text-2xl font-bold text-slate-900">
            Staff Performance
          </h2>
        </div>

        <div
          className="bg-white border border-slate-200 rounded-xl overflow-hidden"
          style={{ boxShadow: "var(--shadow-md)" }}
        >
          <table className="w-full">
            <thead className="bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-slate-200">
              <tr>
                <th className="text-left px-6 py-4 text-sm font-semibold text-slate-700">
                  Staff Member
                </th>
                <th className="text-center px-6 py-4 text-sm font-semibold text-slate-700">
                  Transactions
                </th>
                <th className="text-right px-6 py-4 text-sm font-semibold text-slate-700">
                  Total Sales
                </th>
                <th className="text-center px-6 py-4 text-sm font-semibold text-slate-700">
                  Performance
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {staffPerformance.map((staff, index) => (
                <tr
                  key={index}
                  className="hover:bg-slate-50 transition-colors"
                >
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold">
                        {staff.name
                          .split(" ")
                          .map((n) => n[0])
                          .join("")}
                      </div>
                      <span className="font-semibold text-slate-900">
                        {staff.name}
                      </span>
                    </div>
                  </td>
                  <td className="px-6 py-4 text-center font-semibold text-slate-700">
                    {staff.transactions}
                  </td>
                  <td className="px-6 py-4 text-right font-bold text-purple-600">
                    ₱{staff.totalSales.toLocaleString()}
                  </td>
                  <td className="px-6 py-4 text-center">
                    {index === 0 && (
                      <div className="inline-flex items-center gap-1 text-emerald-600">
                        <TrendingUp className="w-4 h-4" />
                        <span className="text-sm font-semibold">
                          Top Performer
                        </span>
                      </div>
                    )}
                    {index === staffPerformance.length - 1 && (
                      <div className="inline-flex items-center gap-1 text-amber-600">
                        <TrendingDown className="w-4 h-4" />
                        <span className="text-sm font-semibold">
                          Needs Support
                        </span>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}