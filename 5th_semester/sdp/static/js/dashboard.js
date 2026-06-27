document.addEventListener("DOMContentLoaded", async () => {
    // Formatting currency
    const formatCurrency = (val) => {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
    };

    try {
        // Load Summary Data
        const summary = await api.get('/api/dashboard/summary');
        
        document.getElementById('totalRevenue').innerText = formatCurrency(summary.total_revenue);
        
        const revTrend = document.getElementById('revenueTrend');
        revTrend.innerText = summary.revenue_growth || "Not enough historical data";
        if (summary.revenue_growth.includes("↑")) revTrend.style.color = "var(--success)";
        if (summary.revenue_growth.includes("↓")) revTrend.style.color = "var(--danger)";

        document.getElementById('totalExpenses').innerText = formatCurrency(summary.total_expenses);
        document.getElementById('expenseTrend').innerText = summary.expense_ratio || "No expenses yet";
        
        document.getElementById('netProfit').innerText = formatCurrency(summary.net_profit);

        // Chart 1: Monthly Sales (Bar Chart)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const months = summary.monthly_sales.map(s => s.month);
        const salesData = summary.monthly_sales.map(s => s.monthly_sales);
        
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: salesData,
                    backgroundColor: 'rgba(67, 97, 238, 0.7)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Chart 2: Expenses by Category (Doughnut Chart)
        const expCtx = document.getElementById('expenseChart').getContext('2d');
        const categories = summary.expenses_by_category.map(e => e.category);
        const expData = summary.expenses_by_category.map(e => e.category_amount);
        
        // Generate soft colors based on category length
        const bgColors = categories.map((_, i) => `hsl(${i * (360 / categories.length)}, 70%, 60%)`);

        new Chart(expCtx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: expData,
                    backgroundColor: bgColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%'
            }
        });

    } catch (err) {
        console.error("Failed to load dashboard data", err);
    }

    // Load Leaderboard
    try {
        const lbData = await api.get('/api/dashboard/leaderboard');
        const tbTop = document.getElementById('topPerformers');
        const tbBot = document.getElementById('bottomPerformers');
        
        if (!lbData.top || lbData.top.length === 0) {
            tbTop.innerHTML = '<tr><td colspan="2" class="empty-state">No sales data</td></tr>';
            tbBot.innerHTML = '<tr><td colspan="2" class="empty-state">No sales data</td></tr>';
        } else {
            lbData.top.forEach(p => {
                tbTop.innerHTML += `<tr><td style="font-weight: 500;">${p.name}</td><td style="color: var(--success); font-weight: 600;">${formatCurrency(p.revenue)}</td></tr>`;
            });
            lbData.bottom.forEach(p => {
                tbBot.innerHTML += `<tr><td style="font-weight: 500;">${p.name}</td><td style="color: var(--danger); font-weight: 600;">${formatCurrency(p.revenue)}</td></tr>`;
            });
        }
    } catch(err) {
        console.error("Failed to load leaderboard", err);
    }

    // Load Smart Suggestions
    try {
        const suggestionsData = await api.get('/api/dashboard/suggestions');
        const listDiv = document.getElementById('suggestionsList');

        if (!suggestionsData || suggestionsData.length === 0) {
            listDiv.innerHTML = '<div class="empty-state">No suggestions at this time. Great job!</div>';
        } else {
            suggestionsData.forEach(item => {
                const el = document.createElement('div');
                el.className = 'suggestion-item';
                el.innerHTML = `
                    <h4>${item.product_name}</h4>
                    <div class="status">Low Revenue: ${formatCurrency(item.revenue)}</div>
                    <div class="action">💡 Action: ${item.suggestion}</div>
                `;
                listDiv.appendChild(el);
            });
        }
    } catch(err) {
        console.error("Failed to load suggestions", err);
    }
});
