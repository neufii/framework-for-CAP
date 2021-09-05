from numpy.lib.function_base import average
from sklearn.cluster import AgglomerativeClustering
import numpy as np
import json
import sys

fileName = sys.argv[1]
threshold = float(sys.argv[2])

X = np.loadtxt(fileName,delimiter=',')
# X = X + X.T - np.diag(np.diag(X))

model = AgglomerativeClustering(affinity='precomputed', distance_threshold=threshold, n_clusters=None, linkage='single').fit(X)
model = model.fit(X)

unique, counts = np.unique(model.labels_, return_counts=True)

result = {}
result['total_clusters'] = len(unique)
result['average_question_per_clusters'] = np.average(counts)
result['std'] = np.std(counts)
result['questions_in_largest_cluster'] = int(np.max(counts))
result['sample_ids_in_largest_cluster'] = np.where(model.labels_ == np.argmax(counts))[0][:10].tolist()

print(json.dumps(result))
